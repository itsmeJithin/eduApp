<template>
    <SliderModal id="user-details-slider" :show-slider="showDetailsSlider" :options="options" @on-close="onClose">
        <template slot="modal-header" v-if="user">
            <h5 class="mt--1rem mb-3">
                User Details
            </h5>
        </template>
        <template slot="modal-content" v-if="user">
            <div class="row mr-3">
                <div class="col-md-4">
                    <label class="full-width colon-after">Name</label>
                </div>
                <div class="col-md-8 font-weight-bold">
                    {{ user.name }}
                </div>
            </div>
            <div class="row mr-3">
                <div class="col-md-4">
                    <label class="full-width colon-after">Mobile Number</label>
                </div>
                <div class="col-md-8 font-weight-bold">
                    {{ user.mobile_number }}
                </div>
            </div>
            <div class="row mr-3">
                <div class="col-md-4">
                    <label class="full-width colon-after">Parent Number</label>
                </div>
                <div class="col-md-8 font-weight-bold">
                    {{ user.parent_number }}
                </div>
            </div>
            <hr class="mt-2 mb-3">
            <div class="row">
                <div class="col-md-12">
                    <span class="card-heading">Subscription Details</span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th width="45%">Subscribed Months</th>
                            <th width="45%">Subscribed On</th>
                        </tr>
                        </thead>
                        <tbody v-if="subscriptionMonths.length">
                        <tr v-for="(month,index) in subscriptionMonths">
                            <td>
                                {{ index + 1 }}
                            </td>
                            <td>
                                {{ month.subscription_month_name }}
                            </td>
                            <td>
                                {{ month.subscribed_date|formatDate }}
                            </td>
                        </tr>
                        </tbody>
                        <tfoot v-else>
                        <tr>
                            <td colspan="3" class="hint-text text-center font-weight-lighter">
                                <h6>No subscriptions available</h6>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </template>
        <template slot="modal-footer">
            <button class="btn btn-default pull-right" type="button" @click.prevent="onClose">
                <i class="fa fa-times mr-1"></i> Close
            </button>
        </template>
    </SliderModal>
</template>

<script>
import SliderModal from "../common/SliderModal";
import ReportAPIS from "./UsersAPIS";

export default {
    name: "UserDetailsSlider",
    components: {SliderModal},
    props: {
        showDetailsSlider: {
            required: true,
            default: false
        },
        user: {
            required: true
        }
    },
    data() {
        return {
            subscriptionMonths: [],
            options: {
                position: "right",
                size: "medium",
            },
        }
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
    watch: {
        user() {
            if (this.user) {
                this.getUserSubscribedMonths();
            }
        }
    },
    methods: {
        onClose() {
            this.$emit("on-close");
        },
        getUserSubscribedMonths() {
            $('#user-details-slider').find(".modal-dialog").block();
            this.$axios.get(ReportAPIS.GET_USER_SUBSCRIBED_MONTHS, {
                params: {
                    user_id: this.user.user_id
                }
            })
                .then(response => {
                    $('#user-details-slider').find(".modal-dialog").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.subscriptionMonths = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#user-details-slider').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while fetching the subscription details", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
