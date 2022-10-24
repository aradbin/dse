import { reactive } from 'vue'

export const store = reactive({
  // Organizations
  organizations: [],
  loadingOrganizations: true,
  filteredOrganizations: [],
  sectors: [],
  query: {
    search: this.getQueryParameter('search') || null,
    se_index: this.getQueryParameter('se_index') || null,
    category: this.getQueryParameter('category') || null,
    sector: this.getQueryParameter('sector') || null,
    per_page: this.getQueryParameter('per_page') || 20,
    current_page: this.getQueryParameter('current_page') || 1,
    watchlist: this.getQueryParameter('watchlist') || null
  },
  getQueryParameter(key){
    if(window.location.search){
      const urlParams = new URLSearchParams(window.location.search);
      if(urlParams.get(key)){
        return urlParams.get(key);
      }
    }
    return null;
  },
  filterOrganizations(query){
    const arr = this.organizations.filter(function(org){
      let bool = true;
      if(query.portfolio_organizations){
        if(!query.portfolio_organizations.includes(org.id)){
          bool = false;
        }
      }else{
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
        if(query.watchlist && !org.is_watch_listed){
          bool = false;
        }
      }
      return bool;
    }).slice(((query.current_page - 1) * query.per_page), (((query.current_page - 1) * query.per_page) + query.per_page));

    this.filteredOrganizations = arr;
  },
  updateOrganizations(arr){
    this.organizations = arr;
    this.filterOrganizations(this.query);
  },
  updateLoadingOrganizations(bool){
    this.loadingOrganizations = bool;
  },
  updateSectors(arr){
    this.sectors = arr;
  },
  updateOrganization(obj){
    const index = this.organizations.findIndex(org => org.code === obj.code);
    this.organizations[index] = obj;
    this.filterOrganizations(this.query);
  },
  updateQuery(query){
    this.query = query;
    this.filterOrganizations(this.query);
    if(!query.portfolio_organizations){
      let url = window.location.origin + window.location.pathname + '?';
      let count = 1;
      Object.keys(this.query).forEach(function(key) {
        if(count===1){
          url += key + '=' + obj[key];
        }else{
          url += '&' + key + '=' + obj[key];
        }
      });
      window.history.replaceState(null, '', url);
    }
  },
  toggleWatchlist(obj){
    const index = this.organizations.findIndex(org => org.code === obj.code);
    if(this.organizations[index].is_watch_listed){
      this.organizations[index].is_watch_listed = null;
    }else{
      this.organizations[index].is_watch_listed = { 'organization_id': obj.id };
    }
    this.filterOrganizations(this.query);
  },
  async getOrganizationDetails(organizations=[],updatePrice=false){
    await Promise.all(organizations.map((org) => {
      if(updatePrice || !org.price){
        return fetch('/organizations/show/' + org.code)
          .then(response => response.json())
          .then(data => {
            let total_dividend = 0;
            let avg_dividend = 0;
            if(org?.dividends?.length>0){
              org.dividends.map(function(dividend){
                total_dividend = total_dividend + dividend.cash;
              });
              avg_dividend = (((total_dividend/org.dividends.length)/10)/data.LastTrade)*100;
            }
            this.updateOrganization({
              'id' : org.id,
              'code' : org.code,
              'name' : data.FullName,
              'category' : data.MarketCategory,
              'sector' : org.sector,
              'price' : data.LastTrade,
              'eps' : data.EPS,
              'pe' : data.AuditedPE,
              'upe' : data.UnAuditedPE,
              'pnav' : data.NavPrice,
              'pepnav' : (data.AuditedPE * data.NavPrice).toFixed(2),
              'upepnav' : (data.UnAuditedPE * data.NavPrice).toFixed(2),
              'div' : data.DividentYield,
              'agm' : data.LastAGMHeld,
              'listingYear' : data.ListingYear,
              'longLoan' : data.LongLoan,
              'shortLoan' : data.ShortLoan,
              'marketCap' : data.MarketCap,
              'website' : data.Web,
              'is_watch_listed' : org.is_watch_listed,
              'dividends': org.dividends,
              'avg_dividend': avg_dividend.toFixed(2)
            });
          });
      }
      return true;
    }));
  },

  // Portfolio
  portfolios: [],
  updatePortfolios(arr){
    this.portfolios = arr;
  },
  updatePortfolio(obj){
    const index = this.portfolios.findIndex(portfolio => portfolio.id === obj.id);
    this.portfolios[index] = obj;
    
    // Update filtered organizations
    const portfolioOrganizations = [];
    this.portfolios[index].organizations.map((item,orgIndex) => {
      portfolioOrganizations.push(item.organization.id);
    });
    this.updateQuery({
      per_page: 1000,
      current_page: 1,
      portfolio_organizations: portfolioOrganizations
    });
  },
});