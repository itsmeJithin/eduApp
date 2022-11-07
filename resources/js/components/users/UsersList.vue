<template>
    <div class="row" id="user-list">
        <div class="col-md-12">
            <FilterUsers @on-reset="onReset" @on-search="onSearch"/>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Users</h4>
                    <button class="btn btn-outline-primary pull-right" type="button" @click.prevent="createNewUser">
                        <i class="fa fa-plus mr-1"></i> Add New User
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th width="5%">
                                            #
                                        </th>
                                        <th width="20%">
                                            Name
                                        </th>
                                        <th width="15%">
                                            Phone Number
                                        </th>
                                        <th width="10%">
                                            Parent Number
                                        </th>
                                        <th width="15%">
                                            Join Date
                                        </th>
                                        <th width="8%">
                                            Status
                                        </th>
                                        <th width="10%">
                                            More Details
                                        </th>
                                        <th width="10%">
                                            Manage Subsription
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr role="row" :class="(index+1)/2===0?'even':'odd'" v-for="(user,index) in users">
                                        <td class="v-align-middle semi-bold">{{ index + 1 }}</td>
                                        <td class="v-align-middle">{{ user.name }}</td>
                                        <td class="v-align-middle semi-bold">{{ user.mobile_number }}</td>
                                        <td class="v-align-middle">{{ user.parent_number }}</td>
                                        <td class="v-align-middle">{{ user.created_at|formatDate }}</td>
                                        <td class="v-align-middle" :class="user.is_active?'text-success':'text-danger'">
                                            {{ user.is_active ? 'Active' : 'Inactive' }}
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-success"
                                                    type="button" @click.prevent="showDetails(user)">
                                                View Details
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-success"
                                                    type="button" @click.prevent="manageSubscription(user)">
                                                <i class="fa fa-gear mr-1"></i>Manage
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <Pagination class="mt-3" :in-table="true" :pagination-conf="paginationConf"
                                        @page-index-change="getPageData"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <UserDetailsSlider :show-details-slider="showDetailsSlider" :user="selectedUser"
                           @on-close="onDetailsSliderClose"/>
        <AddNewUserSlider :show-slider="showCreateUserSlider" @on-close="closeCreateNewUserSlider"/>
        <ManageUserSubscriptionSlider :show-details-slider="showSubscriptionSlider" :user="manageUserSubscription"
                                      @on-close="closeSubscriptionSlider"/>
    </div>
</template>

<script>
import ReportAPIS from "./UsersAPIS";
import Pagination from "../common/Pagination";
import FilterUsers from "./FilterUsers";
import UserDetailsSlider from "./UserDetailsSlider";
import AddNewUserSlider from "./AddNewUserSlider";
import ManageUserSubscriptionSlider from "./ManageUserSubscriptionSlider";

export default {
    name: "UsersList",
    components: {ManageUserSubscriptionSlider, AddNewUserSlider, UserDetailsSlider, FilterUsers, Pagination},
    data() {
        return {
            showCreateUserSlider: false,
            manageUserSubscription: null,
            showSubscriptionSlider: false,
            showDetailsSlider: false,
            selectedUser: null,
            users: [],
            currentPage: 1,
            paginationConf: {
                "pageIndex": 1,
                "pageSize": 10,
                "total": 0,
                "pageSizeSelectorValues": [10, 20, 30, 40, 50],
            },
            startDate: "",
            endDate: "",
            mobileNumber: "",
            parentMobileNumber: "",
            name: "",
            showSubscribedUsersOnly: ""
        }
    },
    mounted() {
        this.getUsersList(1);
    },
    filters: {
        formatDate(value) {
            if (!value)
                return "-";
            else {
                return moment(value).format("DD-MM-YYYY hh:mm A");
            }
        }
    },
    methods: {
        manageSubscription(user) {
            this.manageUserSubscription = user;
            this.showSubscriptionSlider = true;
        },
        closeSubscriptionSlider() {
            this.manageUserSubscription = null;
            this.showSubscriptionSlider = false;
        },
        createNewUser() {
            this.showCreateUserSlider = true;
        },
        closeCreateNewUserSlider(data) {
            if (data)
                this.users.push(data);
            this.showCreateUserSlider = false;
        },
        onReset() {
            this.startDate = "";
            this.endDate = "";
            this.mobileNumber = "";
            this.parentMobileNumber = "";
            this.name = "";
            this.showSubscribedUsersOnly = false;
            this.getUsersList(1);
        },
        onSearch(data) {
            this.startDate = data.startDate;
            this.endDate = data.endDate;
            this.mobileNumber = data.mobileNumber;
            this.parentMobileNumber = data.parentMobileNumber;
            this.name = data.name;
            this.showSubscribedUsersOnly = data.showSubscribedUsersOnly;
            this.getUsersList(1);
        },
        getPageData(currentPage) {
            if (this.currentPage === currentPage)
                return;
            this.getUsersList(currentPage);
        },
        showDetails(user) {
            this.selectedUser = user;
            this.showDetailsSlider = true;
        },
        onDetailsSliderClose() {
            this.selectedUser = null;
            this.showDetailsSlider = false;
        },
        getUsersList(currentPage) {
            $('#user-list').block();
            const params = {
                "page": currentPage
            };
            if (this.startDate)
                params.start_date = this.startDate;
            if (this.endDate)
                params.end_date = this.endDate;
            if (this.mobileNumber)
                params.mobile_number = this.mobileNumber;
            if (this.name)
                params.name = this.name;
            if (this.parentMobileNumber)
                params.parent_mobile_number = this.parentMobileNumber;
            if (this.showSubscribedUsersOnly)
                params.show_subscribed_users_only = this.showSubscribedUsersOnly;
            this.$axios.get(ReportAPIS.GET_ALL_USERS, {
                params: params
            })
                .then(response => {
                    $('#user-list').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.users = responseData.data.data;
                        this.currentPage = responseData.data.current_page;
                        this.$set(this.paginationConf, "pageIndex", responseData.data.current_page);
                        this.$set(this.paginationConf, "pageSize", responseData.data.per_page);
                        this.$set(this.paginationConf, "total", responseData.data.total);
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#user-list').unblock();
                    $.showNotification("Error occurred while fetching the users list. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
