<template>
    <SliderModal :show-slider="showSlider" id="create-exam-slider" :options="options" @on-close="onClose">
        <template slot="modal-header">
            <h4 class="mt--2rem">
                Create Subject Exam
            </h4>
        </template>
        <template slot="modal-content">
            <form action="" role="form">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.examName.$error }">
                            <label>Exam Name</label>
                            <input type="text" class="form-control" required="" placeholder="Enter the exam name"
                                   v-model.trim="$v.examName.$model" id="exam-name">
                        </div>
                        <label class="error" role="alert" for="exam-name"
                               v-if="$v.examName.$error&&!$v.examName.required">
                            Exam name is required
                        </label>
                        <label class="error" role="alert" for="exam-name"
                               v-if="$v.examName.$error&&!$v.examName.minLength">
                            Exam name length should be at-least than 4 letters
                        </label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group form-group-default">
                            <label>Description</label>
                            <textarea class="form-control" rows="4" v-model.trim="examDescription"
                                      placeholder="Enter the exam description (Optional)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-check form-check-inline switch">
                            <input type="checkbox" id="mock-test" v-model="isMockTest">
                            <label for="mock-test">Is this mock test?</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-2" v-show="!isMockTest">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-check form-check-inline switch">
                            <input type="checkbox" id="is-monthly-exam" v-model="isMonthlyChapterExam">
                            <label for="is-monthly-exam">Is this monthly Chapter Exam?</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-2" v-show="!isMockTest&&!isMonthlyChapterExam">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-check form-check-inline switch">
                            <input type="checkbox" id="is-live-exam" v-model="isLiveExam">
                            <label for="is-live-exam">Is Live Exam?</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-2" v-if="isLiveExam">
                    <div class="col-md-12">
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.startDate.$error }">
                            <label for="start-date">Start Date</label>
                            <DatePicker v-model="$v.startDate.$model" :config="config" id="start-date"/>
                        </div>
                        <label class="error" role="alert" for="start-date"
                               v-if="$v.startDate.$error&&!$v.startDate.required">
                            Select a valid exam start date
                        </label>
                        <label class="error" role="alert" for="start-date"
                               v-if="$v.startDate.$error&&!$v.startDate.minValue">
                            Exam start date should be less than exam end date
                        </label>
                    </div>
                </div>
                <div class="row mt-2" v-if="isLiveExam">
                    <div class="col-md-12">
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.startTime.$error }">
                            <label for="start-time">Start Time</label>
                            <TimePicker v-model="$v.startTime.$model" id="start-time"/>
                        </div>
                        <label class="error" role="alert" for="exam-mode"
                               v-if="$v.startTime.$error&&!$v.startTime.required">
                            Select a valid exam start time
                        </label>
                    </div>
                </div>
                <div class="row mt-2" v-if="isLiveExam">
                    <div class="col-md-12">
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.endDate.$error }">
                            <label for="end-date">End Date</label>
                            <DatePicker v-model="$v.endDate.$model" :config="config" id="end-date"/>
                        </div>
                        <label class="error" role="alert" for="end-date"
                               v-if="$v.endDate.$error&&!$v.endDate.required">
                            Select a valid exam end date
                        </label>
                        <label class="error" role="alert" for="end-date"
                               v-if="$v.endDate.$error&&!$v.endDate.minValue">
                            Exam end date should be greater than exam start date
                        </label>
                    </div>
                </div>
                <div class="row mt-2" v-if="isLiveExam">
                    <div class="col-md-12">
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.startTime.$error }">
                            <label for="end-time">End Time</label>
                            <TimePicker v-model="$v.endTime.$model" :config="config" id="end-time"/>
                        </div>
                        <label class="error" role="alert" for="exam-mode"
                               v-if="$v.endTime.$error&&!$v.endTime.required">
                            Select a valid exam end time
                        </label>
                    </div>
                </div>
                <div class="row mt-2" v-if="isMonthlyChapterExam">
                    <div class="col-md-12">
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.selectedExamMode.$error }">
                            <label>Exam Mode</label>
                            <select name="exam-mode" id="exam-mode" class="form-control"
                                    v-model="$v.selectedExamMode.$model">
                                <option value="" selected disabled>Select exam mode</option>
                                <option :value="mode.exam_mode_id" v-for="mode in examModes">
                                    {{ mode.exam_mode_name }}
                                </option>
                            </select>
                        </div>
                        <label class="error" role="alert" for="exam-mode"
                               v-if="$v.selectedExamMode.$error&&!$v.selectedExamMode.required">
                            Select a valid exam mode
                        </label>
                    </div>
                </div>
                <div class="row mt-2" v-if="isMonthlyChapterExam">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group form-group-default" :class="{ 'has-error': $v.selectedMonth.$error }">
                            <label>Assigned Month</label>
                            <select name="month" id="month" class="form-control" v-model="$v.selectedMonth.$model">
                                <option value="" disabled selected>Select Assigned Month</option>
                                <option :value="month.subscription_month_id"
                                        v-for="month in subscriptionMonths">
                                    {{ month.subscription_month_name }}
                                </option>
                            </select>
                        </div>
                        <label class="error" role="alert" for="month"
                               v-if="$v.selectedMonth.$error&&!$v.selectedMonth.required">
                            Select a valid subscription month
                        </label>
                    </div>
                </div>
                <div class="row mt-2" v-if="isMonthlyChapterExam">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group form-group-default" :class="{ 'has-error': $v.selectedChapter.$error }">
                            <label>Chapter</label>
                            <select name="chapter" id="chapter" class="form-control"
                                    v-model="$v.selectedChapter.$model">
                                <option value="" disabled selected>Select Chapter</option>
                                <option :value="chapter.chapter_id" v-for="chapter in chapters">
                                    {{ chapter.chapter_name }}
                                </option>
                            </select>
                        </div>
                        <label class="error" role="alert" for="exam-name"
                               v-if="$v.selectedChapter.$error&&!$v.selectedChapter.required">
                            Select a valid chapter
                        </label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.maxMarks.$error }">
                            <label for="max-marks">Maximum Marks</label>
                            <input type="number" name="max-marks" id="max-marks" class="form-control"
                                   placeholder="Enter maximum marks"
                                   v-model="$v.maxMarks.$model"/>
                        </div>
                        <label class="error" role="alert" for="max-marks"
                               v-if="$v.maxMarks.$error&&!$v.maxMarks.required">
                            Enter a valid exam maximum marks
                        </label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group form-group-default required" :class="{ 'has-error': $v.maxTime.$error }">
                            <label for="max-time">Maximum Time</label>
                            <input type="number" name="max-time" id="max-time" class="form-control"
                                   placeholder="Enter maximum time for this exam in minutes"
                                   v-model="$v.maxTime.$model"/>
                        </div>
                        <label class="error" role="alert" for="max-time"
                               v-if="$v.maxTime.$error&&!$v.maxTime.required">
                            Enter a valid exam maximum time in minutes
                        </label>
                    </div>
                </div>
            </form>
        </template>
        <template slot="modal-footer">
            <button class="btn-default btn" type="button" @click.prevent="onClose">
                <i class="fa fa-times mr-1"></i> Close
            </button>
            <button class="btn-success btn" type="button" @click.prevent="saveExamDetails">
                <i class="fa fa-save mr-1"></i> Save
            </button>
        </template>
    </SliderModal>
</template>

<script>
import {minLength, required, requiredIf, helpers} from "vuelidate/lib/validators";
import SliderModal from "../common/SliderModal";
import SubscriptionMonthsAPI from "../subscriptionMonths/SubscriptionMonthsAPI";
import ExamsAPI from "./ExamsAPI";
import DatePicker from "../common/DatePicker";
import TimePicker from "../common/TimePicker";

const currentDate = moment().format("DD-MM-YYYY");
const currentTime = moment().format("hh:mm a");
const endTime = moment().add(10, "m").format("hh:mm a");
export default {
    name: "CreateSubjectExamSlider",
    components: {TimePicker, DatePicker, SliderModal},
    props: {
        showSlider: {
            default: false,
            type: Boolean,
            required: true
        },
        classGroupSyllabusSubjectId: {
            required: true
        },
        classGroupSyllabusId: {
            required: true
        },
        selectedExam: {
            type: Object,
            default: () => {
                return null
            }
        }
    },
    data() {
        return {
            startTime: currentTime,
            endTime: endTime,
            startDate: currentDate,
            endDate: currentDate,
            subscriptionMonths: [],
            examModes: [],
            chapters: [],
            selectedChapter: "",
            selectedMonth: "",
            selectedExamMode: "",
            isMockTest: false,
            isLiveExam: false,
            examName: "",
            examDescription: "",
            isMonthlyChapterExam: false,
            maxMarks: "",
            maxTime: "",
            options: {
                position: "right",
                size: "medium",
            },
            config: {
                autoclose: true,
                format: 'dd-mm-yyyy',
                startDate: currentDate
            },
        }
    },
    validations: {
        startDate: {
            required: requiredIf(self => {
                return self.isLiveExam;
            }),
            minValue(val) {
                const startDate = moment(val, "DD-MM-YYYY");
                if (!this.endDate || (this.endDate === val))
                    return true;
                else {
                    return moment(this.endDate, "DD-MM-YYYY").isAfter(startDate);
                }
            }
        },
        startTime: {
            required: requiredIf(self => {
                return self.isLiveExam;
            }),
            minValue(val) {
                if (!this.endTime)
                    return true;
                if (this.startDate === this.endDate) {
                    const endDateTime = `${this.endDate} ${this.endTime}`;
                    const endMoment = moment(endDateTime, "DD-MM-YYYY hh:mm a");
                    const startDateTime = `${this.startDate} ${val}`;
                    const startMoment = moment(startDateTime, "DD-MM-YYYY hh:mm a");
                    return startMoment.isBefore(endMoment);
                }
                return true;
            }
        },
        endDate: {
            required: requiredIf(self => {
                return self.isLiveExam;
            }),
            minValue(val) {
                const endDate = moment(val, "DD-MM-YYYY");
                if (!this.startDate || (this.startDate === val))
                    return true;
                else {
                    return moment(this.startDate, "DD-MM-YYYY").isBefore(endDate);
                }
            }

        },
        endTime: {
            required: requiredIf(self => {
                return self.isLiveExam;
            }),
            minValue(val) {
                if (!this.startTime)
                    return true;
                if (this.startDate === this.endDate) {
                    const endDateTime = `${this.endDate} ${val}`;
                    const endMoment = moment(endDateTime, "DD-MM-YYYY hh:mm a");
                    const startDateTime = `${this.startDate} ${this.startTime}`;
                    const startMoment = moment(startDateTime, "DD-MM-YYYY hh:mm a");
                    return startMoment.isBefore(endMoment);
                }
                return true;
            }
        },
        selectedChapter: {
            required: requiredIf(self => {
                return self.isMonthlyChapterExam
            })
        },
        selectedMonth: {
            required: requiredIf(self => {
                return self.isMonthlyChapterExam;
            })
        },
        selectedExamMode: {
            required: requiredIf(self => {
                return self.isMonthlyChapterExam;
            })
        },
        maxMarks: {
            required,
        },
        maxTime: {
            required
        },
        examName: {
            required,
            minLength: minLength(4)
        }
    },
    mounted() {
        this.getAllExamModes();
    },
    watch: {
        selectedExam() {
            if (this.selectedExam) {
                this.selectedChapter = this.selectedExam.chapter ? this.selectedExam.chapter.chapter_id : "";
                this.selectedMonth = this.selectedExam.subscription_month ? this.selectedExam.subscription_month.subscription_month_id : "";
                this.selectedExamMode = this.selectedExam.exam_mode ? this.selectedExam.exam_mode.exam_mode_id : "";
                this.isMockTest = this.selectedExam.is_mock_test;
                this.isMonthlyChapterExam = this.selectedExam.is_chapter_wise;
                this.maxMarks = this.selectedExam.maximum_marks;
                this.maxTime = this.selectedExam.maximum_time;
                this.examName = this.selectedExam.subject_exam_name;
                this.examDescription = this.selectedExam.subject_exam_description;
                this.isLiveExam = this.selectedExam.is_live_exam;
                const startMoment = moment(this.selectedExam.start_date, "DD-MM-YYYY hh:mm:ss A");
                const endMoment = moment(this.selectedExam.end_date, "DD-MM-YYYY hh:mm:ss A");
                this.startDate = startMoment.format("DD-MM-YYYY");
                this.endDate = endMoment.format("DD-MM-YYYY");
                this.startTime = startMoment.format("hh:mm A");
                this.endTime = endMoment.format("hh:mm A");
            }
        },
        selectedMonth() {
            if (this.selectedMonth) {
                if (!this.selectedMonth)
                    this.selectedChapter = "";
                this.getSubscriptionMonthChapters();
            }
        },
        showSlider() {
            if (this.showSlider)
                this.getAllSubscriptionMonths();
            else
                this.reset();
        },
        isMonthlyChapterExam() {
            this.$v.$reset();
        }
    },
    methods: {
        onClose() {
            this.$emit("on-close");
        },
        reset() {
            this.chapters = [];
            this.selectedChapter = "";
            this.selectedMonth = "";
            this.selectedExamMode = "";
            this.isMockTest = false;
            this.isMonthlyChapterExam = false;
            this.isLiveExam = false;
            this.maxMarks = "";
            this.maxTime = "";
            this.startTime = currentTime;
            this.endTime = endTime;
            this.startDate = currentDate;
            this.endDate = currentDate;
            this.$v.$reset();
        },
        getAllExamModes() {
            this.$axios.get(ExamsAPI.GET_ALL_EXAM_MODES)
                .then(response => {
                    const responseData = response.data;
                    if (responseData.success) {
                        this.examModes = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }

                })
                .catch(() => {
                    $.showNotification("Error occurred while fetching the exam modes", "error");
                })
        },
        getAllSubscriptionMonths() {
            $('#create-exam-slider').find(".modal-dialog").block();
            this.$axios.get(SubscriptionMonthsAPI.GET_ALL_ASSIGNED_SUBSCRIPTION_MONTHS, {
                params: {
                    class_group_syllabus_id: this.classGroupSyllabusId
                }
            })
                .then(response => {
                    $('#create-exam-slider').find(".modal-dialog").unblock();
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
                    $('#create-exam-slider').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while fetching the subscription months", "error");
                })
        },
        getSubscriptionMonthChapters() {
            $('#create-exam-slider').find(".modal-dialog").block();
            this.$axios.get(ExamsAPI.GET_ALL_SUBSCRIPTION_MONTHS_CHAPTERS, {
                params: {
                    class_group_syllabus_id: this.classGroupSyllabusId,
                    class_group_syllabus_subject_id: this.classGroupSyllabusSubjectId,
                    subscription_month_id: this.selectedMonth
                }
            })
                .then(response => {
                    $('#create-exam-slider').find(".modal-dialog").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.chapters = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#create-exam-slider').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while fetching the chapters. Try again later", "error");
                })
        },
        saveExamDetails() {
            this.$v.$touch();
            if (this.$v.$invalid) {
                $.showNotification("Fill all required fields with valid data", "error");
                return
            }
            const data = {
                "class_group_syllabus_subject_id": this.classGroupSyllabusSubjectId,
                "class_group_syllabus_id": this.classGroupSyllabusId,
                "subscription_month_id": this.selectedMonth,
                "exam_name": this.examName,
                "exam_description": this.examDescription,
                "exam_mode": this.selectedExamMode,
                "is_mock_test": this.isMockTest,
                "is_monthly_chapter_exam": this.isMonthlyChapterExam,
                "chapter_id": this.selectedChapter,
                "maximum_marks": this.maxMarks,
                "maximum_time": this.maxTime,
                "is_live_exam": this.isLiveExam,
                "start_date": moment(`${this.startDate} ${this.startTime}`, "DD-MM-YYYY h:mm A").format("DD-MM-YYYY hh:mm:ss A"),
                "end_date": moment(`${this.endDate} ${this.endTime}`, "DD-MM-YYYY h:mm A").format("DD-MM-YYYY hh:mm:ss A")
            }
            if (this.selectedExam) {
                data.cgs_subject_exam_id = this.selectedExam.cgs_subject_exam_id;
            }
            $('#create-exam-slider').find(".modal-dialog").block();
            this.$axios.post(ExamsAPI.CREATE_OR_UPDATE_EXAM, data)
                .then(response => {
                    $('#create-exam-slider').find(".modal-dialog").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        $.showNotification(responseData.message, "success");
                        this.$emit("on-save-success");
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#create-exam-slider').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while saving exam details. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
