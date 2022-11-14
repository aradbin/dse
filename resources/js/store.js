import { reactive } from 'vue'

export const store = reactive({
  // All Organizations
  organizations: [],
  loadingOrganizations: true,
  sectors: [],
  updateOrganizations(arr){
    this.organizations = arr;
  },
  updateOrganization(obj){
    const index = this.organizations.findIndex(org => org.code === obj.code);
    if(index >= 0){
      this.organizations[index] = obj;
    }
    this.filterOrganizations(this.query);
    this.syncPortfolio();
  },
  updateLoadingOrganizations(bool){
    this.loadingOrganizations = bool;
  },
  updateSectors(arr){
    this.sectors = arr;
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
            if(org?.dividends && JSON.parse(org?.dividends).length>0){
              JSON.parse(org?.dividends).map(function(dividend){
                total_dividend = total_dividend + dividend.cash;
              });
              avg_dividend = (((total_dividend/JSON.parse(org?.dividends).length)/10)/data.LastTrade)*100;
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
              'dividends': JSON.stringify(org.dividends),
              'avg_dividend': avg_dividend.toFixed(2)
            });
          });
      }
      return true;
    }));
  },
  

  // Filtered Organizations
  filteredOrganizations: [],
  filteredOrganizationsByPage: [],
  query: {
    search: null,
    se_index: null,
    category: null,
    sector: null,
    per_page: 20,
    current_page: 1,
    watchlist: null
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
  updateQuery(query){
    this.query = query;
    this.filterOrganizations(this.query);
    // let url = window.location.origin + window.location.pathname + '?';
    // let count = 1;
    // Object.keys(this.query).forEach(function(key){
    //   if(query[key]){
    //     if(count===1){
    //       url = url + key + '=' + query[key];
    //     }else{
    //       url = url + '&' + key + '=' + query[key];
    //     }
    //     count++;
    //   }
    // });
    // window.history.replaceState(null, '', url);
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
    });

    this.filteredOrganizations = arr;
    this.filteredOrganizationsByPage = arr.slice(((query.current_page - 1) * query.per_page), (((query.current_page - 1) * query.per_page) + query.per_page));
  },


  // Portfolio
  portfolios: [],
  portfolio: {},
  brokers: [],
  cost: 0,
  value: 0,
  gain: 0,
  gainPercent: 0,
  getPortfolios(){
    fetch('/portfolio/all')
      .then(response => response.json())
      .then(data => {
        this.updatePortfolios(data.portfolios);
        this.updateBrokers(data.brokers);
        this.getPortfolioDetails();
      });
  },
  getPortfolioDetails(updatePrice=false){
    let organizations = [];
    this.portfolios.map((portfolio) => {
      portfolio.organizations?.map((portfolioOrganization) => {
        if(!organizations.find(org => org.code === portfolioOrganization.organization.code)){
          organizations.push(portfolioOrganization.organization);
        }
      });
    });
    this.getOrganizationDetails(organizations,updatePrice);
  },
  updatePortfolios(arr){
    this.portfolios = arr;
  },
  updatePortfolio(obj){
    this.portfolio = {};
    const index = this.portfolios.findIndex(portfolio => portfolio.id === obj.id);
    if(index >= 0){
      this.portfolios[index] = obj;
    }else{
      this.portfolios.push(obj);
    }
    this.portfolio = obj;
    this.syncPortfolio();
  },
  updateBrokers(arr){
    this.brokers = arr;
  },
  syncPortfolio(){
    let totalCost = 0;
    let totalValue = 0;
    let organizations = [];
    this.portfolios.map((portfolio,i) => {
      let portfolioCost = 0;
      let portfolioValue = 0;
      portfolio.organizations.map((portfolioOrganization,j) => {
        const organization = this.organizations.find(org => org.code === portfolioOrganization.organization.code);
        if(organization){
          this.portfolios[i].organizations[j].organization = organization;
          portfolioCost = portfolioCost + (portfolioOrganization.amount * portfolioOrganization.quantity);
          if(organization.price){
            portfolioValue = portfolioValue + (organization.price * portfolioOrganization.quantity);
          }else{
            organizations.push(organization);
          }
        }
      });
      this.portfolios[i].cost = portfolioCost;
      this.portfolios[i].value = portfolioValue;
      this.portfolios[i].gain = (portfolioValue - portfolioCost).toFixed(2);
      this.portfolios[i].gainPercent = (((portfolioValue - portfolioCost) / portfolioCost) * 100).toFixed(2);
      totalCost = totalCost + portfolioCost;
      totalValue = totalValue + portfolioValue;
    });
    this.cost = totalCost;
    this.value = totalValue;
    this.gain = (totalValue - totalCost).toFixed(2);
    this.gainPercent = (((totalValue - totalCost) / totalCost) * 100).toFixed(2);
    if(this.portfolio && Object.keys(this.portfolio).length > 0 && this.portfolios.length > 0){
      this.portfolio = this.portfolios.find(portfolio => portfolio.id===this.portfolio.id);
    }
  }
});