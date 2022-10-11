import { reactive } from 'vue'

export const store = reactive({
  organizations: [],
  updateOrganizations(arr){
    this.organizations = arr;
  }
});