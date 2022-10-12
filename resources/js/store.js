import { reactive } from 'vue'

export const store = reactive({
  organizations: [],
  filteredOrganizations: [],
  query: {
    search: null,
    se_index: null,
    category: null,
    sector: null,
    per_page: 20,
    page: 1,
    watchlist: false
  },
  filterOrganizations(query){
    const arr = this.organizations.filter(function(org){
      let bool = true;
      if(query.search && !org.code.toLowerCase().includes(query.search.toLowerCase())){
        bool = false;
      }
      if(query.se_index && query.se_index!==org.se_index){
        bool = false;
      }
      if(query.category && query.category!==org.category){
        bool = false;
      }
      if(query.sector && query.sector!==org.sector){
        bool = false;
      }
      if(query.watchlist && !org.isWatchListed){
        bool = false;
      }
      return bool;
    }).slice(((query.page - 1) * query.per_page), (((query.page - 1) * query.per_page) + query.per_page));

    this.filteredOrganizations = arr;
  },
  updateOrganizations(arr){
    this.organizations = arr;
    this.filterOrganizations(this.query);
  },
  updateOrganization(obj){
    const index = this.organizations.findIndex(org => org.code === obj.code);
    this.organizations[index] = obj;
    this.filterOrganizations(this.query);
  },
  updateQuery(query){
    this.query = query;
    this.filterOrganizations(this.query);
  },
  toggleWatchlist(obj){
    const index = this.organizations.findIndex(org => org.code === obj.code);
    if(this.organizations[index].isWatchListed){
      this.organizations[index].isWatchListed = null;
    }else{
      this.organizations[index].isWatchListed = { 'organization_id': org.id };
    }
    this.filterOrganizations(this.query);
  }
});