<template>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Filter Users</div>
        </div>
        <div class="card-body">
            <form action="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group form-group-default" :class="{ 'has-error': $v.endDate.$error }">
                            <label>Joining Start Date</label>
                            <DatePicker :config="config" id="join-start-date" v-model="$v.startDate.$model"/>
                        </div>
                        <label class="error" role="alert" for="join-start-date"
                               v-if="$v.startDate.$error&&!$v.startDate.minValue">
                            Joining start date should be less than joining end date
                        </label>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-group-default" :class="{ 'has-error': $v.endDate.$error }">
                            <label>Joining End Date</label>
                            <DatePicker :config="config" id="join-end-date" v-model="$v.endDate.$model"/>
                        </div>
                        <label class="error" role="alert" for="join-end-date"
                               v-if="$v.endDate.$error&&!$v.endDate.minValue">
                            Joining end date should be greater than joining start date
                        </label>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-group-default">
                            <label>Mobile Number</label>
                            <input type="text" class="form-control" placeholder="Example: 9064523721"
                                   v-model="mobileNumber">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group form-group-default">
                            <label>Parent Mobile Number</label>
                            <input type="text" class="form-control" placeholder="Example: 7865436532"
                                   v-model="parentMobileNumber">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-group-default">
                            <label>Name</label>
                            <input type="text" class="form-control" placeholder="Example: John Martine" v-model="name">
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center" v-if="false">
                        <div class="form-check ">
                            <input type="checkbox" id="defaultCheck" v-model="showSubscribedUsersOnly">
                            <label for="defaultCheck">
                                Show subscribed users only
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-default" @click.prevent="onReset">
                            <i class="fa fa-times mr-1"></i> Reset
                        </button>
                        <button class="btn btn-success" @click.prevent="onSearch">
                            <i class="fa fa-search mr-1"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import DatePicker from "../common/DatePicker";
import {requiredIf} from "vuelidate/lib/validators";

export default {
    name: "FilterUsers",
    components: {DatePicker},
    data() {
        return {
            config: {
                autoclose: true,
                format: 'dd-mm-yyyy',
            },
            startDate: "",
            endDate: "",
            mobileNumber: "",
            parentMobileNumber: "",
            name: "",
            showSubscribedUsersOnly: false
        }
    },
    validations: {
        startDate: {
            required: false,
            minValue(val) {
                if (this.endDate && val) {
                    const startDate = moment(val, "DD-MM-YYYY");
                    return moment(this.endDate, "DD-MM-YYYY").isAfter(startDate);
                }
                return true;
            }
        },
        endDate: {
            required: false,
            minValue(val) {
                if (this.startDate && val) {
                    const endDate = moment(val, "DD-MM-YYYY");
                    return moment(this.startDate, "DD-MM-YYYY").isBefore(endDate);
                }
                return true;
            }

        },
    },
    methods: {
        onReset() {
            this.startDate = "";
            this.endDate = "";
            this.mobileNumber = "";
            this.parentMobileNumber = "";
            this.name = "";
            this.showSubscribedUsersOnly = false;
            this.$v.$reset();
            this.$emit("on-reset");
        },
        onSearch() {
            this.$v.$touch();
            if (this.$v.$invalid) {
                $.showNotification("Invalid date search field values", "error");
                return
            }
            if (this.startDate === "" && this.endDate === "" && this.mobileNumber === "" && this.parentMobileNumber === "" && this.name === "" && !this.showSubscribedUsersOnly) {
                $.showNotification("You should fill one of the above fields to search users", "error");
                return;
            }

            const data = {
                "startDate": this.startDate,
                "endDate": this.endDate,
                "mobileNumber": this.mobileNumber,
                "parentMobileNumber": this.parentMobileNumber,
                "name": this.name,
                "showSubscribedUsersOnly": this.showSubscribedUsersOnly
            }
            this.$emit("on-search", data);
        }
    }
}
</script>

<style scoped>

</style>
