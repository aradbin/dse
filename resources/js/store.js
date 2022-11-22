import { reactive } from 'vue'

export const store = reactive({
  // All Organizations
  organizations: [],
  loadingOrganizations: true,
  sectors: [],
  updateOrganizations(arr){
    this.organizations = arr;
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
    if(query.search!==this.query.search || query.se_index!==this.query.se_index || query.category!==this.query.category || query.sector!==this.query.sector){
      query.current_page = 1;
    }
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
  loadingPortfolios: true,
  getPortfolios(){
    this.loadingPortfolios = true;
    fetch('/portfolio/all')
      .then(response => response.json())
      .then(data => {
        this.loadingPortfolios = false;
        this.updatePortfolios(data.portfolios);
        this.updateBrokers(data.brokers);
      });
  },
  updatePortfolios(arr){
    this.portfolios = arr;
    this.syncPortfolio();
  },
  updatePortfolio(obj){
    this.portfolio = {};
    this.portfolio = JSON.parse(JSON.stringify(obj));
    const index = this.portfolios.findIndex(portfolio => portfolio.id === this.portfolio.id);
    if(index >= 0){
      this.portfolios[index] = this.portfolio;
    }else{
      this.portfolios.push(this.portfolio);
    }
    this.syncPortfolio();
  },
  updateBrokers(arr){
    this.brokers = arr;
  },
  syncPortfolio(){
    let totalCost = 0;
    let totalValue = 0;
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
          }
        }
      });
      this.portfolios[i].cost = portfolioCost.toFixed(2);
      this.portfolios[i].value = portfolioValue.toFixed(2);
      this.portfolios[i].gain = (portfolioValue - portfolioCost).toFixed(2);
      this.portfolios[i].gainPercent = (((portfolioValue - portfolioCost) / portfolioCost) * 100).toFixed(2);
      if(isNaN(this.portfolios[i].gainPercent)){ this.portfolios[i].gainPercent = 0 };
      totalCost = totalCost + portfolioCost;
      totalValue = totalValue + portfolioValue;
    });
    this.cost = totalCost.toFixed(2);
    this.value = totalValue.toFixed(2);
    this.gain = (totalValue - totalCost).toFixed(2);
    this.gainPercent = (((totalValue - totalCost) / totalCost) * 100).toFixed(2);
    if(isNaN(this.gainPercent)){ this.gainPercent = 0 };
    if(this.portfolio && Object.keys(this.portfolio).length > 0 && !this.loadingOrganizations && !this.loadingPortfolios){
      let index = this.portfolios.findIndex(portfolio => portfolio.id===this.portfolio.id);
      if(index >= 0){
        this.portfolio = this.portfolios[index];
      }else{
        this.getPortfolios();
      }
    }
  }
});