import { reactive } from 'vue'

export const store = reactive({
  // Organizations
  organizations: [],
  filteredOrganizations: [],
  sectors: [],
  query: {
    search: null,
    se_index: null,
    category: null,
    sector: null,
    per_page: 20,
    page: 1,
    watchlist: null
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
      if(query.watchlist && !org.is_watch_listed){
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
  getOrganization(code){
    return this.organizations.filter(org => org.code === code)[0];
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
    
    // Update organization
    this.portfolios[index].organizations.map((item,orgIndex) => {
      this.portfolios[index].organizations[orgIndex] = this.organizations.filter((org) => {
        if(org.code===item.code){
          return true;
        }
        return false;
      })[0];
    });
  },
});