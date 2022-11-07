<template>
    <SliderModal :show-slider="showSlider" id="show-exam-result-slider" :options="options" @on-close="onClose">
        <template slot="modal-header">
            <h4 class="mt--2rem">
                Exam Result
            </h4>
        </template>
        <template slot="modal-content">
            <table class="table table-condensed table-bordered">
                <thead>
                <tr>
                    <th width="15%">#</th>
                    <th width="55%">Name</th>
                    <th width="30%">Mark</th>
                </tr>
                </thead>
                <tbody v-if="students.length!==0">
                <tr v-for="(student,index) in students">
                    <td>{{ ++index }}</td>
                    <td>{{ student.user.name }}</td>
                    <td>{{ student.exam_mark ? student.exam_mark.marks_obtained : "Not Completed" }}</td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="3" class="hint-text text-center">
                        No students attended yet
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
        <template slot="modal-footer">
            <button class="btn-default btn" type="button" @click.prevent="onClose">
                <i class="fa fa-times mr-1"></i> Close
            </button>
        </template>
    </SliderModal>
</template>

<script>
import ExamsAPI from "./ExamsAPI";
import SliderModal from "../common/SliderModal";

export default {
    components: {
        SliderModal
    },
    name: "ExamResults",
    props: {
        exam: {
            required: true,
        },
        showSlider: {
            default: false,
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            students: [],
            options: {
                position: "right",
                size: "large",
            },
        }
    },
    watch: {
        exam() {
            if (this.exam) {
                this.getAllExamRegistrations();
            } else {
                this.students = [];
            }
        }
    },
    methods: {
        onClose() {
            this.$emit("on-close");
            this.students = [];
        },
        getAllExamRegistrations() {
            this.$axios.get(ExamsAPI.GET_ALL_EXAM_RESULTS, {
                params: {
                    cgs_subject_exam_id: this.exam.cgs_subject_exam_id
                }
            })
                .then(response => {
                    const data = response.data;
                    if (data.success) {
                        this.students = data.data;
                    } else {
                        $.showNotification(data.error, "error");
                    }
                })
                .catch(() => {
                    $.showNotification("Error occurred while fetching the exam results. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
