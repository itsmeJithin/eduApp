<template>
    <div class="tab-pane active" id="subscription-months">
        <div class="row column-seperation">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
                        <table class="table  dataTable no-footer" id="stripedTable" role="grid">
                            <thead>
                            <tr role="row">
                                <th width="5%">#</th>
                                <th width="20%">
                                    Subscription Months
                                </th>
                                <th width="20%">
                                    Assign Fee
                                </th>
                                <th width="10%">
                                    Save
                                </th>
                            </tr>
                            </thead>
                            <tbody v-if="subscriptionMonths.length">

                            <tr role="row" class="odd" v-for="(month,index) in subscriptionMonths">
                                <td>
                                    {{index+1}}
                                </td>
                                <td class="v-align-middle semi-bold sorting_1">
                                    {{month.subscription_month_name}}
                                </td>
                                <td class="v-align-middle">
                                    <div class="form-group input-group transparent">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text transparent">
                                            &#x20B9;
							            </span>
                                        </div>
                                        <input type="number" placeholder="Enter the amount" class="form-control"
                                               v-model="month.price">
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-success" @click.prevent="saveMonthFee(month)"
                                            :id="'monthly-fee-'+month.subscription_month_id">
                                        <i class="fa fa-check mr-1"></i> Save
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot v-else>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <h5 class="hint-text font-weight-lighter">
                                        You are not assigned any months to selected class group
                                    </h5>
                                    <a href="" @click="assignSubscriptionMonths">
                                        <i class="fa fa-plus mr-1"></i> Click here to assign subscription month
                                    </a>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CourseFeeAPI from "./CourseFeeAPI";

    export default {
        name: "MonthlyFee",
        props: {
            classGroupSyllabusId: {
                required: true,
                type: String
            }
        },
        data() {
            return {
                subscriptionMonths: [],
            }
        },
        mounted() {
            if (this.classGroupSyllabusId)
                this.getAllSubscriptionMonths();
        },
        watch: {
            classGroupSyllabusId() {
                if (this.classGroupSyllabusId)
                    this.getAllSubscriptionMonths();
            }
        },
        methods: {
            getAllSubscriptionMonths() {
                $('#subscription-months').block();
                this.$axios.get(CourseFeeAPI.GET_ASSIGNED_MONTHLY_FEE, {
                    params: {
                        class_group_syllabus_id: this.classGroupSyllabusId
                    }
                })
                    .then(response => {
                        $('#subscription-months').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            if (responseData.data)
                                this.subscriptionMonths = responseData.data.subscription_months;
                            else
                                $.showNotification("Invalid request sent! We couldn't find any class groups");
                        } else
                            $.showNotification(responseData.error, "error");
                    })
                    .catch(() => {
                        $('#subscription-months').unblock();
                        $.showNotification("Error occurred while fetching the subscription months", "error");
                    })
            },
            saveMonthFee(month) {
                $('#monthly-fee-' + month.subscription_month_id).loading();
                const data = {
                    class_group_syllabus_id: this.classGroupSyllabusId,
                    subscription_month_id: month.subscription_month_id,
                    price: month.price
                };
                this.$axios.post(CourseFeeAPI.SAVE_MONTHLY_FEE, data)
                    .then(response => {
                        $('#monthly-fee-' + month.subscription_month_id).resetLoading();
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification(responseData.message);
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#monthly-fee-' + month.subscription_month_id).resetLoading();
                        $.showNotification("Error occurred while saving the fee. Try again later", "error");
                    })

            },
            assignSubscriptionMonths() {
                this.$router.push({
                    name: "subscriptionMonths",
                    params: {classGroupSyllabusId: this.class_group_syllabus_id}
                })
            }
        }
    }
</script>

<style scoped>

</style>
