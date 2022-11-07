<template>
    <div class="tab-pane slide-left padding-20 sm-no-padding active" id="tab11">
        <div class="card-heading">
            Create New Question
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <label for="question-creator">Question</label>
                <QuillTextEditor v-model="content"
                                 :id="'question-creator'"/>
            </div>
        </div>
        <div class="row mt-3 row-same-height">
            <div class="col-md-6 col-sm-12" v-for="(option,index) in answerOptions">
                <MCQAnswer :option="option" :index="index"
                           :key="option.id"
                           @on-remove-option="onRemoveOption"/>
            </div>
            <div class="col-md-6 " @click.prevent="addNewOption">
                <div class="add-mcq-option b-a b-dashed b-grey min-h-83-per text-center hint-text">
                    <i class="fa fa-plus mr-2"></i>
                    {{ answerOptions.length ? 'Add another option' : 'Add Option' }}
                </div>
                <hr class="mt-3 mb-3"/>
            </div>
        </div>
        <hr>
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>Mark</label>
                    <input type="number" class="form-control" required v-model="mark"
                           placeholder="Enter the mark for this question">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group form-group-default">
                    <label>Time <span class="hint-text">(in seconds)</span></label>
                    <input type="number" class="form-control" v-model="time"
                           placeholder="Enter the time required to complete this question">
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <button class="btn btn-default" type="button" @click.prevent="reset">
                    <i class="fa fa-times mr-1"></i> Clear Fields
                </button>
                <button class="btn btn-success" type="button" @click.prevent="saveQuestion">
                    <i class="fa fa-save mr-1"></i> Save Question
                </button>
            </div>
        </div>
        <hr>
        <div class="row mt-2" v-if="classGroupSyllabusSubjectId">
            <div class="col-md-12">
                <h3 class="card-heading">Available Questions</h3>
                <AvailableQuestions
                    :is-data-changes="dataChanged"
                    :class-group-syllabus-subject-id="classGroupSyllabusSubjectId"
                    @on-edit="editQuestion"
                />
            </div>
        </div>
    </div>
</template>

<script>
import _ from "lodash";
import QuillTextEditor from "../common/QuillTextEditor";
import MCQAnswer from "./MCQAnswer";
import AvailableQuestions from "./AvailableQuestions";
import QuestionPoolAPI from "./QuestionPoolAPI";

export default {
    name: "ManageQuestion",
    components: {AvailableQuestions, MCQAnswer, QuillTextEditor},
    data() {
        const dt = new Date().getTime();
        return {
            dataChanged: false,
            classGroupSyllabusSubjectId: "",
            quillOptions: {
                modules: {
                    // 'better-table': true,
                    toolbar: [
                        [{"header": [1, 2, 3, 4, 5, false]}],
                        [{"font": []}],
                        ["bold", "italic", "underline", "strike"],
                        ["blockquote", "code-block", "link"],
                        [{"list": "ordered"}, {"list": "bullet"}],
                        [{"script": "sub"}, {"script": "super"}],
                        [{"color": []}, {"background": []}],
                        [{"align": []}],
                        [{"indent": "+1"}, {"indent": "-1"}],
                    ],
                    clipboard: {
                        allowed: {
                            tags: ['a', 'b', 'strong', 'u', 's', 'i', 'p', 'br', 'ul', 'ol', 'li', 'span'],
                            attributes: ['href', 'rel', 'target', 'class']
                        },
                        keepSelection: false,
                    }
                },
                placeholder: "Enter you answer option here"
            },
            editorSettings: {
                cut: true,
                copy: true,
                paste: true
            },
            content: "",
            answerOptions: [{
                id: dt,
                answer: "",
                isRightAnswer: false
            }],
            mark: "",
            time: "",
            selectedQuestion: null
        }
    },
    mounted() {
        const route = this.$route;
        this.classGroupSyllabusSubjectId = route.params.classGroupSyllabusSubjectId;
    },
    watch: {
        selectedQuestion() {
            if (this.selectedQuestion) {
                this.content = this.selectedQuestion.question;
                this.answerOptions = JSON.parse(this.selectedQuestion.options);
                this.mark = this.selectedQuestion.mark;
                this.time = this.selectedQuestion.question_time;
            }
        }
    },
    methods: {
        changeDataChangedFlag() {
            this.dataChanged = false;
        },
        editQuestion(question) {
            this.selectedQuestion = question;
        },
        onRemoveOption(data) {
            console.log(data);
            const options = _.cloneDeep(this.answerOptions);
            _.remove(options, {id: data.id});
            this.answerOptions = _.cloneDeep(options);
        },
        addNewOption() {
            const dt = new Date().getTime();
            this.answerOptions.push({
                id: dt,
                answer: "",
                isRightAnswer: false
            })
        },
        reset() {
            this.selectedQuestion = null;
            const dt = new Date().getTime();
            this.mark = "";
            this.time = "";
            this.answerOptions = [{
                id: dt,
                answer: "",
                isRightAnswer: false
            }];
            this.content = "";
        },
        saveQuestion() {
            if (!this.content) {
                $.showNotification("You should enter the question in given editor", "error");
                return;
            }
            if (this.answerOptions.length < 2) {
                $.showNotification("You should create at-lease 2 options to create a question", "error");
                return;
            }
            if (!this.mark) {
                $.showNotification("You should enter mark for this question", "error");
                return;
            }
            const rightAnswer = _.find(this.answerOptions, {"isRightAnswer": true});
            if (!rightAnswer) {
                $.showNotification("You should mark one option as right answer", "error");
                return;
            }
            const data = {
                question: this.content,
                options: JSON.stringify(this.answerOptions),
                mark: this.mark,
                question_time: this.time,
                class_group_syllabus_subject_id: this.classGroupSyllabusSubjectId,
            }
            if (this.selectedQuestion) {
                data.question_id = this.selectedQuestion.question_id;
            }
            this.$axios.post(QuestionPoolAPI.SAVE_QUESTION_DETAILS, data)
                .then(response => {
                    const responseData = response.data;
                    if (responseData.success) {
                        this.dataChanged = true;
                        this.reset();
                        $.showNotification(responseData.message, "success");
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $.showNotification("Error occurred while saving the question details. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
