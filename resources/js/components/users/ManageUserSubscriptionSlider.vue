<template>
    <SliderModal id="manage-subscription-slider" :show-slider="showDetailsSlider" :options="options"
                 @on-close="onClose">
        <template slot="modal-header" v-if="user">
            <h5 class="mt--1rem mb-3">
                Manage Subscriptions
            </h5>
        </template>
        <template slot="modal-content" v-if="user">
            <div class="row">
                <div class="col-md-12">
                    <form role="form">
                        <div class="form-group form-group-default required"
                             :class="{'has-error':$v.selectedSyllabus.$error}">
                            <label for="syllabus">Syllabus</label>
                            <select name="syllabus" id="syllabus" class="form-control select2"
                                    v-model="$v.selectedSyllabus.$model">
                                <option value="" disabled>
                                    Select syllabus
                                </option>
                                <option :value="syllabus.syllabus_id" v-for="syllabus in syllabuses">
                                    {{ syllabus.syllabus_name }}
                                </option>
                            </select>
                        </div>
                        <label class="error" role="alert" for="syllabus"
                               v-if="$v.selectedSyllabus.$error&&!$v.selectedSyllabus.required">
                            Select a valid syllabus
                        </label>
                        <div class="form-group form-group-default required">
                            <label for="syllabus">Course</label>
                            <select name="course" id="course" class="form-control select2" v-model="selectedCourse">
                                <option value="" disabled>
                                    Select Course
                                </option>
                                <option :value="course.course_id" v-for="course in courses">
                                    {{ course.course_name }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group form-group-default required">
                            <label for="course">Class</label>
                            <select name="course" id="class" class="form-control select2" v-model="selectedClass"
                                    :disabled="!selectedCourse">
                                <option value="" disabled>
                                    Select Class
                                </option>
                                <option :value="courseClass.class_id" v-for="courseClass in classes">
                                    {{ courseClass.class_name }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group form-group-default required"
                             :class="{'has-error':$v.selectedClassGroup.$error}">
                            <label for="class-group">Class Group</label>
                            <select name="class-group" id="class-group" class="form-control select2"
                                    v-model="$v.selectedClassGroup.$model" :disabled="!selectedClass">
                                <option value="" disabled>
                                    Select Class Group
                                </option>
                                <option :value="group.class_group_id" v-for="group in classGroups">
                                    {{ group.class_group_name }}
                                </option>
                            </select>
                        </div>
                        <label class="error" role="alert" for="class-group"
                               v-if="$v.selectedClassGroup.$error&&!$v.selectedClassGroup.required">
                            Select a valid class group
                        </label>
                        <div class="form-group text-right mt-3">
                            <button class="btn btn-default" @click.prevent="reset">
                                <i class="fa fa-times mr-1"></i> Reset
                            </button>
                            <button class="btn btn-primary" @click.prevent="getUserNotSubscribedMonths">
                                <i class="fa fa-filter mr-1"></i> Get Subscription Months
                            </button>
                        </div>
                    </form>
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
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th width="30%">Months</th>
                            <th width="20%">Price</th>
                            <th width="30%">Paid Amount</th>
                            <th width="10%">Assign</th>
                        </tr>
                        </thead>
                        <tbody v-if="subscriptionMonths.length">
                        <AssignSubscriptionMonthRow v-for="(month,index) in subscriptionMonths"
                                                    :key="month.subscription_month_id"
                                                    :month="month"
                                                    :index="index"/>
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
            <button class="btn btn-success" type="button" v-if="subscriptionMonths.length"
                    @click.prevent="assignMonthsToUser">
                <i class="fa fa-plus mr-1"></i> Add subscriptions to user
            </button>
        </template>
    </SliderModal>
</template>

<script>
import SliderModal from "../common/SliderModal";
import UsersAPIS from "./UsersAPIS";
import {required} from "vuelidate/lib/validators";
import ClassGroupAPI from "../classGroups/ClassGroupAPI";
import ClassesAPI from "../classes/ClassesAPI";
import SubjectAPI from "../subjects/SubjectAPI";
import AssignSubscriptionMonthRow from "./AssignSubscriptionMonthRow";

export default {
    name: "ManageUserSubscriptionSlider",
    components: {AssignSubscriptionMonthRow, SliderModal},
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
                size: "large",
            },
            courses: [],
            selectedCourse: "",
            classes: [],
            selectedClass: "",
            classGroups: [],
            selectedClassGroup: "",
            syllabuses: [],
            selectedSyllabus: ""
        }
    },
    validations: {
        selectedSyllabus: {
            required
        },
        selectedClassGroup: {
            required
        }
    },
    watch: {
        selectedCourse() {
            this.classGroups = [];
            this.classes = [];
            this.selectedClass = "";
            this.selectedClassGroup = "";
            if (this.selectedCourse)
                this.getClassesByCourse();
        },
        selectedClass() {
            this.classGroups = [];
            this.selectedClassGroup = "";
            if (this.selectedClass)
                this.getClassGroupsByClass();
        }
    },
    mounted() {
        this.getAllBasicDetails();
    },
    methods: {
        getClassGroupsByClass() {
            $('#manage-subscription-slider').find(".modal-dialog").block();
            this.$axios.get(ClassGroupAPI.GET_ALL_CLASS_GROUPS_BY_CLASS, {
                params: {
                    class_id: this.selectedClass
                }
            })
                .then(response => {
                    $('#manage-subscription-slider').find(".modal-dialog").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.classGroups = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#manage-subscription-slider').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while fetching the class list. Try again later", "error");
                })
        },
        getClassesByCourse() {
            $('#manage-subscription-slider').find(".modal-dialog").block();
            this.$axios.get(ClassesAPI.GET_CLASSES_BY_COURSE, {
                params: {
                    course_id: this.selectedCourse
                }
            })
                .then(response => {
                    $('#manage-subscription-slider').find(".modal-dialog").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.classes = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#manage-subscription-slider').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while fetching the class list. Try again later", "error");
                })
        },
        getAllBasicDetails() {
            $('#manage-subscription-slider').find(".modal-dialog").block();
            this.$axios.get(SubjectAPI.GET_ALL_BASIC_DETAILS)
                .then(response => {
                    $('#manage-subscription-slider').find(".modal-dialog").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.courses = responseData.data.courses;
                        this.syllabuses = responseData.data.syllabuses;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#manage-subscription-slider').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while fetching some form details. Try again later", "error");
                })
        },
        reset() {
            this.selectedCourse = "";
            this.classes = [];
            this.selectedClass = "";
            this.classGroups = [];
            this.selectedClassGroup = "";
            this.selectedSyllabus = "";
            this.subscriptionMonths = [];
            this.$v.$reset();
        },
        onClose() {
            this.reset();
            this.$emit("on-close");
        },
        getUserNotSubscribedMonths() {
            this.$v.$touch();
            if (this.$v.$invalid) {
                $.showNotification("Select all required fields", "error");
                return
            }
            $('#manage-subscription-slider').find(".modal-dialog").block();
            this.$axios.get(UsersAPIS.GET_USER_NOT_SUBSCRIBED_MONTHS, {
                params: {
                    user_id: this.user.user_id,
                    class_group_id: this.selectedClassGroup,
                    syllabus_id: this.selectedSyllabus
                }
            })
                .then(response => {
                    $('#manage-subscription-slider').find(".modal-dialog").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.subscriptionMonths = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#manage-subscription-slider').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while fetching the subscription details", "error");
                })
        },
        assignMonthsToUser() {
            const assignedMonths = _.filter(_.map(this.subscriptionMonths, month => {
                if (month.is_assigned) {
                    return {
                        subscription_month_id: month.subscription_month_id,
                        paid_amount: month.paid_amount,
                        class_group_syllabus_id: month.class_group_syllabus_id,
                        syllabus_subscription_month_id: month.syllabus_subscription_month_id
                    }
                }
                return false;
            }), 'subscription_month_id');
            if (!assignedMonths.length) {
                $.showNotification("You should assign at-least one month to the selected user", "error");
            }
            this.$axios.post(UsersAPIS.ASSIGN_SUBSCRIPTION_MONTHS, {
                user_id: this.user.user_id,
                assigned_months: assignedMonths
            })
                .then(response => {
                    const responseData = response.data;
                    if (responseData.success) {
                        this.onClose();
                        $.showNotification("Subscription months added successfully", "success");
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $.showNotification("Error occurred while assigning subscriptions. Try again later", "error");
                })

        }
    }
}
</script>

<style scoped>

</style>
