<template>
    <div class="card ">
        <div class="card-header ">
            <div class="card-title">
                Assigned Subjects
            </div>
            <div class="tools">
            </div>
        </div>
        <div class="card-body" id="subject-list">
            <div class="table-responsive">
                <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
                    <table class="table  dataTable no-footer" id="stripedTable" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="5%">#</th>
                            <th width="20%">
                                Subject Name
                            </th>
                            <th width="20%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                Subject Code
                            </th>
                            <th width="45%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                Subject Description
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                Manage Chapters
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                Edit/Delete
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr role="row" class="odd" v-for="(subject,index) in subjects">
                            <td>
                                {{index+1}}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{subject.subject_name}}
                            </td>
                            <td class="v-align-middle">
                                {{subject.subject_code}}
                            </td>
                            <td class="v-align-middle">
                                {{subject.subject_description}}
                            </td>
                            <td class="v-align-middle">
                                <button class="btn btn-outline-success" @click.prevent="manageChapters(subject)">
                                    <i class="fa fa-plus mr-1"></i> Manage Chapters
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-sm p-0 btn-default" @click.prevent="updateSubject(subject)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 btn-danger ml-1"
                                        @click.prevent="deleteSubject(subject)">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!subjects.length">
                            <td colspan="5" class="hint-text text-center">
                                <h5 class="font-weight-light">No subjects assigned to this class group</h5>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import SubjectAPI from "./SubjectAPI";

    export default {
        name: "ListSubjects",
        props: {
            classGroupSyllabusId: {
                type: String,
                default: ""
            },
            fetchSubjectList: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                subjects: [],
            }
        },
        mounted() {
            if (this.classGroupSyllabusId) {
                this.getSubjectsByClassGroupSyllabus()
            }
        },
        watch: {
            fetchSubjectList() {
                if (this.fetchSubjectList)
                    this.getSubjectsByClassGroupSyllabus();
            }
        },
        methods: {
            manageChapters(subject) {
                this.$router.push({
                    name: "designClassSubjects",
                    params: {classGroupSyllabusId: this.classGroupSyllabusId, subjectId: subject.subject_id}
                })
            },
            getSubjectsByClassGroupSyllabus() {
                $('#subject-list').block();
                this.$axios.get(SubjectAPI.GET_ALL_SUBJECTS_CLASS_GROUP_SYLLABUS_ID, {
                    params: {
                        class_group_syllabus_id: this.classGroupSyllabusId,
                    }
                })
                    .then(response => {
                        this.$emit("on-list-fetched");
                        $('#subject-list').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.subjects = responseData.data;
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        this.$emit("on-list-fetched");
                        $('#subject-list').unblock();
                        $.showNotification("Error occurred while fetching the subject details. Try again later", "error");
                    })
            },
            showCreateModal() {
                this.showModal = true;
            },
            onModalClose(status) {
                this.isUpdate = false;
                this.selectedSyllabus = null;
                if (status)
                    this.getAllSyllabuses();
            },
            updateSubject(subject) {
                this.$emit("on-edit-subject", subject);
            },
            deleteSubject(subject) {
                const self = this;
                $.simpleDialog({
                    title: "Do you want to delete?",
                    message: "Deleting of subject is not possible until removing all chapters from the subjects. Do you want to continue?",
                    confirmBtnText: "Yes! Delete",
                    closeBtnText: "No! Cancel",
                    confirmBtnClass: "btn-danger",
                    closeBtnClass: "btn-success",
                    onSuccess: function () {
                        self.delete(subject);
                    }
                });
            },
            delete(subject) {
                $('#subject-list').block();
                this.$axios.delete(SubjectAPI.DELETE_SUBJECT, {
                    params: {
                        "class_group_syllabus_id": subject.class_group_syllabus_id,
                        "syllabus_id": syllabus.subject_id
                    }
                })
                    .then(response => {
                        $('#subject-list').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification("Subject deleted successfully", "success");
                            this.getSubjectsByClassGroupSyllabus();
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#subject-list').unblock();
                        $.showNotification("Error occurred while deleting subject. Try again later", "error");
                    })
            }
        }
    }
</script>

<style scoped>

</style>
