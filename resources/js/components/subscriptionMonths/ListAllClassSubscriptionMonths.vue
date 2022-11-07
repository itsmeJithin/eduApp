<template>
    <div class="card ">
        <div class="card-header ">
            <div class="card-title">
                Assign and Arrange Syllabus Months
                <p class="hint-text fs-11 text-transform-none">You can assign months from the available list and you can
                    also arrange the months
                    as you wish. This order will be reflected in the user application.</p>
            </div>
            <div class="tools">
            </div>
        </div>
        <div class="card-body" id="months-list">
            <div class="row">
                <div class="col-md-6 b-r b-dashed">
                    <h6 class="font-weight-light">Assigned Months</h6>
                    <draggable v-model="assignedSubscriptionMonths" group="people"
                               @start="drag=true" @end="drag=false">
                        <div class="card card-default card-bordered mb-1 draggable-card"
                             v-for="element in assignedSubscriptionMonths"
                             :key="element.subscription_month_id">
                            <div class="card-header" role="tab">
                                <a href="" class="pt-0 pb-0 mt--2rem ">
                                    <i class="material-icons fs-14 tab-icon">drag_indicator</i>
                                </a>
                                <div class="card-title">
                                    <a href="#">
                                        {{element.subscription_month_name}}
                                    </a>
                                </div>
                                <a href="" class="pull-right pt-0 pb-0 mt--2rem text-danger"
                                   @click.prevent="deleteMonth(element)">
                                    <i class="fa fa-times fs-12"></i>
                                </a>
                            </div>
                        </div>
                    </draggable>
                    <div class="card card-default" v-if="!assignedSubscriptionMonths.length">
                        <div class="card-body text-center">
                            <h6 class="font-weight-light">You haven't assigned any months. You can assign months from
                                the available list.</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="font-weight-light">Available Months</h6>
                    <div class="card card-default card-bordered mb-1 draggable-card"
                         v-for="month in availableSubscriptionMonths"
                         :key="month.subscription_month_id">
                        <div class="card-header" role="tab">
                            <div class="card-title">
                                <a href="#">
                                    {{month.subscription_month_name}}
                                </a>
                            </div>
                            <a href="" class="pull-right pt-0 pb-0 mt--2rem text-success"
                               @click.prevent="assignMonth(month)">
                                <i class="fa fa-plus fs-12"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card card-default" v-if="!availableSubscriptionMonths.length">
                        <div class="card-body text-center">
                            <h6 class="font-weight-light">You've assigned all months</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row  mt-3">
                <div class="col-md-12 text-center">
                    <button class="btn btn-default mr-1" @click.prevent="goBack">
                        <i class="fa fa-chevron-circle-left mr-1"></i> Go Back
                    </button>
                    <button class="btn btn-default mr-1" @click.prevent="reset">
                        <i class="fa fa-times mr-1"></i> Reset Changes
                    </button>
                    <button class="btn btn-primary" @click.prevent="assignSubscriptionMonths">
                        <i class="fa fa-save mr-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import _ from "lodash";
    import draggable from "vuedraggable";
    import SubscriptionMonthsAPI from "./SubscriptionMonthsAPI";

    export default {
        name: "ListAllClassSubscriptionMonths",
        components: {
            draggable
        },
        data() {
            return {
                subscriptionMonths: [],
                classGroupSyllabus: null,
                assignedSubscriptionMonths: [],
                availableSubscriptionMonths: [],
                classGroupSyllabusId: "",
                deletedMonths: []
            }
        },
        mounted() {
            this.decideManageSubjectShow();
        },
        watch: {
            $route(to) {
                this.classGroupSyllabusId = to.params.classGroupSyllabusId;
            }
        },
        methods: {
            goBack() {
                this.$router.push({
                    name: "designClassSubjects"
                });
            },
            reset() {
                this.subscriptionMonths = [];
                this.classGroupSyllabus = null;
                this.assignedSubscriptionMonths = [];
                this.availableSubscriptionMonths = [];
                this.deletedMonths = [];
                this.getAllCGSSubscriptionMonths();
            },
            decideManageSubjectShow() {
                const route = this.$route;
                this.classGroupSyllabusId = route.params.classGroupSyllabusId;
                if (this.classGroupSyllabusId) {
                    this.getAllCGSSubscriptionMonths();
                }
            },
            getAllCGSSubscriptionMonths() {
                $('#months-list').block();
                this.$axios.get(SubscriptionMonthsAPI.GET_CLASS_SUBSCRIPTION_MONTHS, {
                    params: {
                        class_group_syllabus_id: this.classGroupSyllabusId
                    }
                })
                    .then(response => {
                        $('#months-list').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.classGroupSyllabus = responseData.data.classGroupSyllabus;
                            this.subscriptionMonths = responseData.data.availableMonths;
                            if (responseData.data.classGroupSyllabus) {
                                this.assignedSubscriptionMonths = responseData.data.classGroupSyllabus.subscription_months;
                                this.availableSubscriptionMonths = _.differenceBy(this.subscriptionMonths, this.assignedSubscriptionMonths, "subscription_month_id");
                            }

                        } else
                            $.showNotification(responseData.error, "error");
                    })
                    .catch(() => {
                        $('#months-list').unblock();
                        $.showNotification("Error occurred while fetching the class group subscription months. Try again later", "error");
                    })
            },
            deleteMonth(month) {
                if (month.pivot) {
                    this.deletedMonths.push(month.subscription_month_id);
                }
                this.availableSubscriptionMonths.push(month);
                const months = _.cloneDeep(this.assignedSubscriptionMonths);
                _.remove(months, {"subscription_month_id": month.subscription_month_id});
                this.assignedSubscriptionMonths = months;

            },
            assignMonth(month) {
                if (month.pivot) {
                    const months = _.clone(this.deletedMonths);
                    _.remove(months, (item) => {
                        return item === month.subscription_month_id;
                    });
                    this.deletedMonths = months;
                }
                const subscriptionMonths = _.cloneDeep(this.availableSubscriptionMonths);
                _.remove(subscriptionMonths, {"subscription_month_id": month.subscription_month_id});
                this.availableSubscriptionMonths = subscriptionMonths;
                this.assignedSubscriptionMonths.push(month);
            },
            assignSubscriptionMonths() {
                const data = {
                    "assignedSubscriptionMonths": JSON.stringify(this.assignedSubscriptionMonths),
                    "deletedSubscriptionMonths": this.deletedMonths,
                    "class_group_syllabus_id": this.classGroupSyllabusId
                };
                $('#months-list').block();
                this.$axios.post(SubscriptionMonthsAPI.ASSIGN_SUBSCRIPTION_MONTHS, data)
                    .then(response => {
                        const responseData = response.data;
                        $('#months-list').unblock();
                        if (responseData.success) {
                            this.getAllCGSSubscriptionMonths();
                            $.showNotification("Subscription months assigned successfully", "success");
                        } else {
                            $.showNotification(responseData.error, "error", 10000);
                        }
                    })
                    .catch(() => {
                        $('#months-list').unblock();
                        $.showNotification("Error occurred while assigning subscription months", "error");
                    })
            }
        }
    }
</script>

<style scoped>
    .no-border {
        border: 0 !important;
    }

</style>
