<template>
  <div class="custom-tab-content slide-left sm-no-padding active" id="manage-subject-exam">
    <div class="row ">
      <div class="col-md-12">
        <span class="card-heading">Available Exams</span>
        <button class="btn btn-primary pull-right" @click.prevent="createExam">
          <i class="fa fa-plus mr-1"></i> Create Exam
        </button>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col-md-12">
        <div class="table-responsive" id="exam-list-table">
          <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
            <table class="table  dataTable no-footer" id="stripedTable" role="grid">
              <thead>
              <tr role="row">
                <th width="5%">#</th>
                <th width="15%">
                  Exam Name
                </th>
                <th width="20%">
                  Exam Description
                </th>
                <th width="15%">
                  Chapter
                </th>
                <th width="15%">
                  Subscription Month
                </th>
                <th width="5%">
                  Number of Questions
                </th>
                <th width="7%">
                  Manage Questions
                </th>
                <th width="7%">
                  Status
                </th>
                <th width="7%">
                  Results
                </th>
                <th width="8%">
                  Edit/Delete
                </th>
              </tr>
              </thead>
              <tbody>

              <tr role="row" :class="(index+1/2)===0?'odd':'even'" v-for="(exam,index) in exams">
                <td class="v-align-middle">
                  {{ index + 1 }}
                </td>
                <td class="v-align-middle semi-bold sorting_1">
                  {{ exam.subject_exam_name }}
                </td>
                <td class="v-align-middle">
                  {{ exam.subject_exam_description }}
                </td>
                <td class="v-align-middle">
                  {{ exam.chapter ? exam.chapter.chapter_name : '-' }}
                </td>
                <td class="v-align-middle">
                  {{
                    exam.subscription_month ? exam.subscription_month.subscription_month_name : '-'
                  }}
                </td>
                <td class="v-align-middle">
                  {{ exam.questions_count }}
                </td>
                <td class="v-align-middle">
                  <button class="btn btn-outline-success" @click.prevent="manageQuestions(exam)"
                          type="button"
                          :disabled="isDisabled(exam)">
                    <i class="fa fa-gear mr-1"></i> Manage Questions
                  </button>
                </td>
                <td class="v-align-middle">
                  <button class="btn btn-outline-primary" @click.prevent="togglePublish(exam)"
                          v-if="!exam.is_published">
                    <i class="fa fa-check mr-1"></i> Publish
                  </button>
                  <button class="btn btn-outline-danger" @click.prevent="togglePublish(exam)" v-else>
                    <i class="fa fa-times mr-1"></i> Un-Publish
                  </button>
                </td>
                <td class="v-align-middle">
                  <button class="btn btn-outline-info" @click.prevent="viewResults(exam)">
                    View Results
                  </button>
                </td>
                <td class="v-align-middle">
                  <button class="btn btn-sm p-0 btn-default" @click.prevent="updateExam(exam)"
                          :disabled="isDisabled(exam)">
                    <i class="fa fa-pencil"></i>
                  </button>
                  <button class="btn btn-sm p-0 btn-danger ml-1"
                          @click.prevent="deleteExam(exam)">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </td>
              </tr>
              <tr v-if="!exams.length">
                <td colspan="9" class="hint-text text-center">
                  <h5 class="font-weight-light">No exams created for this selected subject</h5>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <CreateSubjectExamSlider :show-slider="showSlider"
                             :class-group-syllabus-subject-id="classGroupSyllabusSubjectId"
                             :class-group-syllabus-id="classGroupSyllabusId"
                             :selected-exam="selectedExam"
                             @on-save-success="onSaveSuccess"
                             @on-close="onClose"/>
    <ExamResults :show-slider="showResultSlider"
                 :exam="selectedExam"
                 @on-close="onResultSliderClose"/>
  </div>
</template>

<script>
import CreateSubjectExamSlider from "./CreateSubjectExamSlider";
import ExamResults from "./ExamResults";
import ExamsAPI from "./ExamsAPI";

export default {
  name: "CreateSubjectExam",
  components: {ExamResults, CreateSubjectExamSlider},
  props: {
    classGroupSyllabusSubjectId: {
      required: true
    },
    classGroupSyllabusId: {
      required: true
    }
  },
  data() {
    return {
      showSlider: false,
      exams: [],
      selectedExam: null,
      showResultSlider: false,
    }
  },
  mounted() {
    this.getAllExams();
  },
  methods: {
    viewResults(exam) {
      this.showResultSlider = true;
      this.selectedExam = exam;
    },
    onResultSliderClose() {
      this.showResultSlider = false;
      this.selectedExam = null;
    },
    isDisabled(exam) {
      return exam.is_published ? 'disabled' : false;
    },
    onSaveSuccess() {
      this.getAllExams()
      this.onClose()
    },
    onClose() {
      this.selectedExam = null;
      this.showSlider = false;
    },
    createExam() {
      this.showSlider = true;
    },
    getAllExams() {
      $('#exam-list-table').block();
      this.$axios.get(ExamsAPI.GET_ALL_EXAMS, {
        params: {
          class_group_syllabus_subject_id: this.classGroupSyllabusSubjectId
        }
      })
          .then(response => {
            $('#exam-list-table').unblock();
            const responseData = response.data;
            if (responseData.success) {
              this.exams = responseData.data;
            } else {
              $.showNotification(responseData.error, "error");
            }
          })
          .catch(() => {
            $('#exam-list-table').unblock();
            $.showNotification("Error occurred while fetching the exams, Try again later", "error");
          })
    },
    manageQuestions(exam) {
      this.$router.push({
        name: 'exams',
        params: {
          examId: exam.cgs_subject_exam_id,
          classGroupSyllabusId: this.classGroupSyllabusId,
          classGroupSyllabusSubjectId: this.classGroupSyllabusSubjectId
        }
      })
    },
    updateExam(exam) {
      console.log(exam);
      this.selectedExam = exam;
      this.showSlider = true;
    },
    togglePublish(exam) {
      this.$axios.post(ExamsAPI.TOGGLE_PUBLISH, {
        cgs_subject_exam_id: exam.cgs_subject_exam_id
      })
          .then(response => {
            const responseData = response.data;
            if (responseData.success) {
              this.$set(exam, "is_published", !exam.is_published);
              $.showNotification("Exam status changed successfully", "success");
            } else {
              $.showNotification(responseData.error, "error");
            }
          })
          .catch(() => {
            $.showNotification("Error occurred while changing exam status. Try again later", "error");
          })
    },
    deleteExam(exam) {
      const self = this;
      $.simpleDialog({
        title: "Do you want to delete this exam?",
        message: "Deleting of exam is not possible if students already attended this exam. Do you want to continue?",
        confirmBtnText: "Yes! Delete",
        closeBtnText: "No! Cancel",
        confirmBtnClass: "btn-danger",
        closeBtnClass: "btn-success",
        onSuccess: function () {
          self.delete(exam);
        }
      });
    },
    delete(exam) {
      this.$axios.delete(ExamsAPI.DELETE_SUBJECT_EXAM, {
        params: {
          cgs_subject_exam_id: exam.cgs_subject_exam_id
        }
      })
          .then(response => {
            const responseData = response.data;
            if (responseData.success) {
              $.showNotification("Exam deleted successfully", "success");
              this.getAllExams();
            } else {
              $.showNotification(responseData.error, "error");
            }
          })
          .catch(() => {
            $.showNotification("Error occurred while deleting exam. Try again later", "error");
          })
    },
  }
}
</script>

<style scoped>

</style>
