import { reactive } from 'vue'

export const store = reactive({
  organizations: [],
  filterOrganizations(query){
    this.organizations = this.organizations.filter(function(org){
      let bool = true;
      if(query.search && !org.code.includes(query.search)){
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
    });
  },
  updateOrganizations(arr){
    this.organizations = arr;
  },
  updateOrganization(obj){
    index = this.organizations.findIndex(org => org.code === obj.code);
    this.organizations[index] = obj;
  },
  toggleWatchlist(obj){
    index = this.organizations.findIndex(org => org.code === obj.code);
    if(this.organizations[index].isWatchListed){
      this.organizations[index].isWatchListed = null;
    }else{
      this.organizations[index].isWatchListed = { 'organization_id': org.id };
    }
  }
});