<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'users' => Auth::user()->account->users()
                ->orderByName()
                ->filter(Request::only('search', 'role', 'trashed'))
                ->get()
                ->transform(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'owner' => $user->owner,
                    'photo' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path, 'w' => 40, 'h' => 40, 'fit' => 'crop']) : null,
                    'deleted_at' => $user->deleted_at,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store()
    {
        Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')],
            'password' => ['nullable'],
            'owner' => ['required', 'boolean'],
            'photo' => ['nullable', 'image'],
        ]);

        Auth::user()->account->users()->create([
            'first_name' => Request::get('first_name'),
            'last_name' => Request::get('last_name'),
            'email' => Request::get('email'),
            'password' => Request::get('password'),
            'owner' => Request::get('owner'),
            'photo_path' => Request::file('photo') ? Request::file('photo')->store('users') : null,
        ]);

        return Redirect::route('users')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'owner' => $user->owner,
                'photo' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path, 'w' => 60, 'h' => 60, 'fit' => 'crop']) : null,
                'deleted_at' => $user->deleted_at,
            ],
        ]);
    }

    public function update(User $user)
    {
        if (App::environment('demo') && $user->isDemoUser()) {
            return Redirect::back()->with('error', 'Updating the demo user is not allowed.');
        }

        Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable'],
            'owner' => ['required', 'boolean'],
            'photo' => ['nullable', 'image'],
        ]);

        $user->update(Request::only('first_name', 'last_name', 'email', 'owner'));

        if (Request::file('photo')) {
            $user->update(['photo_path' => Request::file('photo')->store('users')]);
        }

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
        }

        return Redirect::back()->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if (App::environment('demo') && $user->isDemoUser()) {
            return Redirect::back()->with('error', 'Deleting the demo user is not allowed.');
        }

        $user->delete();

        return Redirect::back()->with('success', 'User deleted.');
    }

    public function restore(User $user)
    {
        $user->restore();

        return Redirect::back()->with('success', 'User restored.');
    }

    public function watchlist()
    {
        $per_page = 20;
        if(Request::get('per_page')){
            $per_page = Request::get('per_page');
        }
        return Inertia::render('Organizations/Index', [
            'filters' => Request::all('search', 'se_index', 'category', 'sector', 'per_page'),
            'sectors' => Organization::groupBy('sector')->select('sector')->get(),
            'organizations' => Organization::join('watchlists','watchlists.organization_id','organizations.id')
                ->where('watchlists.user_id',Auth::user()->id)
                ->with('dividends')
                ->orderBy('organizations.code')
                ->filter(Request::only('search', 'se_index', 'category', 'sector'))
                ->select('organizations.id','organizations.code','organizations.category','organizations.sector','watchlists.id as watchlisted')
                ->paginate($per_page)
                ->withQueryString()
                ->through(fn ($organization) => [
                    'id' => $organization->id,
                    'code' => $organization->code,
                    'name' => null,
                    'category' => $organization->category,
                    'sector' => $organization->sector,
                    'price' => null,
                    'eps' => null,
                    'pe' => null,
                    'upe' => null,
                    'pnav' => null,
                    'pepnav' => null,
                    'upepnav' => null,
                    'div' => null,
                    'agm' => null,
                    'listingYear' => null,
                    'longLoan' => null,
                    'shortLoan' => null,
                    'marketCap' => null,
                    'website' => null,
                    'watchlisted' => $organization->watchlisted,
                    'dividends' => json_encode($organization->dividends),
                    'avg_dividend' => null
                ]),
        ]);
    }
}
