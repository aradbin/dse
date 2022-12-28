import { reactive } from 'vue'

export const store = reactive({
  // All Organizations
  organizations: [],
  loadingOrganizations: true,
  sectors: [],
  updateOrganizations(arr,syncPortfolio=false){
    this.organizations = arr;
    if(syncPortfolio){
      this.syncPortfolio();
    }
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
    if(query.current_page===this.query.current_page){
      query.current_page = 1;
    }
    this.query = JSON.parse(JSON.stringify(query));
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
  balance: 0,
  deposit: 0,
  withdraw: 0,
  expense: 0,
  paid_commission: 0,
  paid_charge: 0,
  paid_tax: 0,
  income: 0,
  realized_gain: 0,
  cash_dividend: 0,
  profit: 0,
  buy: 0,
  loadingPortfolios: true,
  async getPortfolios(){
    this.loadingPortfolios = true;
    await fetch('/portfolio/all')
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
    let totalGain = 0;
    let totalExpense = 0;
    let totalIncome = 0;
    let totalProfit = 0;
    let totalBuy = 0;
    let totalBalance = 0;
    let totalDeposit = 0;
    let totalWithdraw = 0;
    let totalPaidCommission = 0;
    let totalPaidCharge = 0;
    let totalPaidTax = 0;
    let totalRealizedGain = 0;
    let totalCashDividend = 0;
    this.portfolios.map((portfolio,i) => {
      let portfolioCost = 0;
      let portfolioValue = 0;
      let portfolioGain = 0;
      let portfolioExpense = 0;
      let portfolioIncome = 0;
      let portfolioProfit = 0;
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
      portfolioGain = portfolioValue - portfolioCost;
      portfolioExpense = portfolio.paid_commission + portfolio.paid_charge + portfolio.paid_tax;
      portfolioIncome = portfolio.realized_gain + portfolio.cash_dividend + portfolioGain;
      portfolioProfit = portfolioIncome - portfolioExpense;

      this.portfolios[i].cost = portfolioCost.toFixed(2);
      this.portfolios[i].value = portfolioValue.toFixed(2);
      this.portfolios[i].gain = portfolioGain.toFixed(2);
      this.portfolios[i].gainPercent = ((portfolioGain / portfolioCost) * 100).toFixed(2);
      if(isNaN(this.portfolios[i].gainPercent)){ this.portfolios[i].gainPercent = 0 };
      this.portfolios[i].expense = portfolioExpense.toFixed(2);
      this.portfolios[i].income = portfolioIncome.toFixed(2);
      this.portfolios[i].profit = portfolioProfit.toFixed(2);
      this.portfolios[i].profitPercent = ((portfolioProfit / portfolio.buy) * 100).toFixed(2);
      if(isNaN(this.portfolios[i].profitPercent)){ this.portfolios[i].profitPercent = 0 };
      
      totalCost = totalCost + portfolioCost;
      totalValue = totalValue + portfolioValue;
      totalGain = totalGain + portfolioGain;
      totalExpense = totalExpense + portfolioExpense;
      totalIncome = totalIncome + portfolioIncome;
      totalProfit = totalProfit + portfolioProfit;
      totalBuy = totalBuy + portfolio.buy;
      totalBalance = totalBalance + portfolio.balance;
      totalDeposit = totalDeposit + portfolio.deposit;
      totalWithdraw = totalWithdraw + portfolio.withdraw;
      totalPaidCommission = totalPaidCommission + portfolio.paid_commission;
      totalPaidCharge = totalPaidCharge + portfolio.paid_charge;
      totalPaidTax = totalPaidTax + portfolio.paid_tax;
      totalRealizedGain = totalRealizedGain + portfolio.realized_gain;
      totalCashDividend = totalCashDividend + portfolio.cash_dividend;
    });

    this.cost = totalCost.toFixed(2);
    this.value = totalValue.toFixed(2);
    this.gain = (totalValue - totalCost).toFixed(2);
    this.gainPercent = (((totalValue - totalCost) / totalCost) * 100).toFixed(2);
    if(isNaN(this.gainPercent)){ this.gainPercent = 0 };
    this.expense = totalExpense.toFixed(2);
    this.income = totalIncome.toFixed(2);
    this.profit = totalProfit.toFixed(2);
    this.profitPercent = ((totalProfit / totalBuy) * 100).toFixed(2);
    if(isNaN(this.profitPercent)){ this.profitPercent = 0 };
    this.balance = totalBalance;
    this.deposit = totalDeposit;
    this.withdraw = totalWithdraw;
    this.paid_commission = totalPaidCommission;
    this.paid_charge = totalPaidCharge;
    this.paid_tax = totalPaidTax;
    this.realized_gain = totalRealizedGain;
    this.cash_dividend = totalCashDividend;
    this.buy = totalBuy;
    
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