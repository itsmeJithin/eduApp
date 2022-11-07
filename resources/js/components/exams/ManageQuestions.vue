<template>
    <div class="custom-tab-content slide-left sm-no-padding active" id="manage-exam-questions">
        <div class="row ">
            <div class="col-md-12">
                <span class="card-heading">{{ exam ? exam.subject_exam_name + ' Questions' : 'Exam Questions' }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 b-r b-dashed">
                <h6 class="font-weight-light">Assigned Questions</h6>
                <draggable v-model="assignedQuestions" group="people"
                           @start="drag=true" @end="drag=false">
                    <div class="card card-default card-bordered mb-1 draggable-card"
                         v-for="element in assignedQuestions"
                         :key="element.question_id">
                        <div class="card-header" role="tab">
                            <a href="" class="pt-0 pb-0 mt--2rem ">
                                <i class="material-icons fs-14 tab-icon v-align-top">drag_indicator</i>
                            </a>
                            <div class="card-title exam-questions">
                                <a href="#" v-html="element.question">
                                    {{ element.question }}
                                </a>
                            </div>
                            <a href="" class="pull-right pt-0 pb-0 mt--2rem text-danger"
                               @click.prevent="removeQuestion(element)">
                                <i class="fa fa-times fs-14"></i>
                            </a>
                            <a href="" class="pull-right pt-0 pb-0 mt--2rem hint-text mr-2"
                               @click.prevent="showQuestionDetails(element)">
                                <i class="fa fa-eye fs-14"></i>
                            </a>
                        </div>
                    </div>
                </draggable>
                <div class="card card-default" v-if="!assignedQuestions.length">
                    <div class="card-body text-center">
                        <h6 class="font-weight-light">You haven't assigned any questions to the selected exam. You can
                            assign months from
                            the available list.</h6>
                    </div>
                </div>
                <button class="btn btn-success pull-right mt-2" type="button" @click.prevent="saveChanges"
                        v-if="assignedQuestions.length||deletedQuestions.length">
                    <i class="fa fa-save mr-1"></i> Save Changes
                </button>
            </div>
            <div class="col-md-6">
                <h6 class="font-weight-light">Available Questions</h6>
                <div class="card card-default card-bordered mb-1 draggable-card"
                     v-for="question in availableQuestions"
                     :key="question.question_id">
                    <div class="card-header" role="tab">
                        <div class="card-title exam-questions">
                            <a href="#" v-html="question.question">
                                {{ question.question }}
                            </a>
                        </div>
                        <a href="" class="pull-right pt-0 pb-0 mt--2rem text-success"
                           @click.prevent="assignQuestion(question)">
                            <i class="fa fa-plus fs-14"></i>
                        </a>
                        <a href="" class="pull-right pt-0 pb-0 mt--2rem hint-text mr-2"
                           @click.prevent="showQuestionDetails(question)">
                            <i class="fa fa-eye fs-14"></i>
                        </a>
                    </div>
                </div>
                <div class="card card-default" v-if="!availableQuestions.length">
                    <div class="card-body text-center">
                        <h6 class="font-weight-light" v-if="assignedQuestions.length">
                            You've assigned all available questions
                        </h6>
                        <h6 class="font-weight-light" v-else>
                            You've not created any questions
                        </h6>
                    </div>
                </div>
            </div>
        </div>
        <ViewQuestionDetails :show-slider="showDetails"
                             :question="selectedQuestion"
                             :show-assign-button="true"
                             @on-assign="assignQuestion"
                             @on-close="onSliderClose"/>
    </div>
</template>

<script>
import _ from "lodash";
import draggable from "vuedraggable";
import ExamsAPI from "./ExamsAPI";
import ViewQuestionDetails from "../questionPool/ViewQuestionDetails";

export default {
    name: "ManageQuestions",
    components: {
        ViewQuestionDetails,
        draggable
    },
    props: {
        examId: {
            required: true,
            type: String,
        },
        classGroupSyllabusId: {
            type: String
        },
        classGroupSyllabusSubjectId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            availableQuestions: [],
            assignedQuestions: [],
            exam: null,
            questions: [],
            deletedQuestions: [],
            showDetails: false,
            selectedQuestion: null
        }
    },
    mounted() {
        this.getAllAssignedAndAvailableQuestions()
    },
    methods: {
        onSliderClose() {
            this.showDetails = false;
            this.selectedQuestion = null;
        },
        showQuestionDetails(question) {
            this.showDetails = true;
            this.selectedQuestion = question;
        },
        getAllAssignedAndAvailableQuestions() {
            $('#manage-exam-questions').block();
            this.$axios.get(ExamsAPI.GET_ALL_ASSIGNED_AVAILABLE_QUESTIONS, {
                params: {
                    exam_id: this.examId,
                    class_group_syllabus_subject_id: this.classGroupSyllabusSubjectId
                }
            })
                .then(response => {
                    $('#manage-exam-questions').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.exam = responseData.data.exam;
                        this.questions = responseData.data.availableQuestions;
                        if (responseData.data.exam) {
                            this.assignedQuestions = this.exam.questions;
                            this.availableQuestions = _.differenceBy(this.questions, this.assignedQuestions, "question_id");
                        }
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#manage-exam-questions').unblock();
                    $.showNotification("Error occurred while fetching the questions. Try again later", "error");
                })
        },
        assignQuestion(question) {
            if (question.pivot) {
                const questions = _.clone(this.deletedQuestions);
                _.remove(questions, (item) => {
                    return item === question.question_id;
                });
                this.deletedQuestions = questions;
            }
            const availableItems = _.cloneDeep(this.availableQuestions);
            _.remove(availableItems, {"question_id": question.question_id});
            this.availableQuestions = availableItems;
            this.assignedQuestions.push(question);

        },
        removeQuestion(question) {
            if (question.pivot) {
                this.deletedQuestions.push(question.question_id);
            }
            this.availableQuestions.push(question);
            const questions = _.cloneDeep(this.assignedQuestions);
            _.remove(questions, {"question_id": question.question_id});
            this.assignedQuestions = questions;
        },
        saveChanges() {
            const assignedQuestionIds = _.map(this.assignedQuestions, (item) => {
                return item.question_id
            })
            const data = {
                "assigned_questions": assignedQuestionIds,
                "deleted_questions": this.deletedQuestions,
                "exam_id": this.examId
            };
            $('#manage-exam-questions').block();
            this.$axios.post(ExamsAPI.ASSIGN_QUESTIONS_TO_EXAM, data)
                .then(response => {
                    const responseData = response.data;
                    $('#manage-exam-questions').unblock();
                    if (responseData.success) {
                        this.getAllAssignedAndAvailableQuestions();
                        $.showNotification("Changes saved successfully", "success");
                    } else {
                        $.showNotification(responseData.error, "error", 10000);
                    }
                })
                .catch(() => {
                    $('#manage-exam-questions').unblock();
                    $.showNotification("Error occurred while assigning subscription months", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
