<template>
    <div class="table-responsive" id="questions-list">
        <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
            <table class="table table-condensed no-footer" id="stripedTable" role="grid">
                <thead>
                <tr role="row">
                    <th width="5%">#</th>
                    <th width="60%">
                        Question
                    </th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable">
                        Mark
                    </th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable">
                        Time (Seconds)
                    </th>
                    <th width="15%" class="sorting" tabindex="0" aria-controls="stripedTable">
                        Edit/Delete
                    </th>
                </tr>
                </thead>
                <tbody>

                <tr role="row" class="odd" v-for="(question,index) in questions">
                    <td>
                        {{ index + 1 }}
                    </td>
                    <td class="v-align-middle semi-bold sorting_1" >
                        {{ question.question |strippedContent }}
                    </td>
                    <td class="v-align-middle">
                        {{ question.mark }}
                    </td>
                    <td class="v-align-middle">
                        {{ question.question_time ? question.question_time : '-' }}
                    </td>
                    <td class="v-align-middle">
                        <button class="btn btn-sm p-0 btn-default" @click.prevent="updateQuestion(question)">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-sm p-0 btn-danger ml-1"
                                @click.prevent="deleteQuestion(question)">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </td>
                </tr>
                <tr v-if="!questions.length">
                    <td colspan="5" class="hint-text text-center">
                        <h5 class="font-weight-light">No questions created for this subject</h5>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import QuestionPoolApi from "./QuestionPoolAPI"

export default {
    name: "AvailableQuestions",
    props: {
        classGroupSyllabusSubjectId: {
            required: true,
        },
        isDataChanges: {
            default: false,
            type: Boolean
        }
    },
    filters: {
        strippedContent: function (string) {
            const data= string.replace(/<\/?[^>]+(>|$)/g, "");
            console.log(data);
            return data;
        }
    },
    data() {
        return {
            questions: []
        }
    },
    mounted() {
        if (this.classGroupSyllabusSubjectId) {
            this.getAllQuestions();
        }
    },
    watch: {
        classGroupSyllabusSubjectId() {
            if (this.classGroupSyllabusSubjectId) {
                this.getAllQuestions();
            }
        },
        isDataChanges() {
            if (this.isDataChanges) {
                this.getAllQuestions();
            }
        }
    },
    methods: {
        getAllQuestions() {
            $('#questions-list').block();
            this.$axios.get(QuestionPoolApi.GET_ALL_SUBJECT_QUESTIONS, {
                params: {
                    class_group_syllabus_subject_id: this.classGroupSyllabusSubjectId
                }
            })
                .then(response => {
                    $('#questions-list').unblock();
                    this.$emit("on-data-fetched");
                    const responseData = response.data;
                    if (responseData.success) {
                        this.questions = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#questions-list').unblock();
                    this.$emit("on-data-fetched");
                    $.showNotification("Error occurred while fetching the questions. Try again later", "error");
                })
        },
        updateQuestion(question) {
            this.$emit("on-edit", question)
        },
        deleteQuestion(question) {
            const self = this;
            $.simpleDialog({
                title: "Do you want to delete?",
                message: "Deleting of question is not possible until removing questions from the exams. Do you want to continue?",
                confirmBtnText: "Yes! Delete",
                closeBtnText: "No! Cancel",
                confirmBtnClass: "btn-danger",
                closeBtnClass: "btn-success",
                onSuccess: function () {
                    self.delete(question);
                }
            });
        },
        delete(question) {
            this.$axios.delete(QuestionPoolApi.DELETE_QUESTION, {
                params: {
                    question_id: question.question_id
                }
            })
                .then(response => {
                    const responseData = response.data;
                    if (responseData.success) {
                        this.getAllQuestions();
                        $.showNotification(responseData.message, "success");
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $.showNotification("Error occurred while deleting the question. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
