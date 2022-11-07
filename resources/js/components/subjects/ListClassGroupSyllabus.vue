<template>
    <div class="card ">
        <div class="card-header ">
            <div class="card-title">
                Assigned Class Group Syllabus
            </div>
            <div class="tools">
            </div>
        </div>
        <div class="card-body" id="syllabus-list">
            <div class="table-responsive">
                <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
                    <table class="table  dataTable no-footer" id="stripedTable" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="5%">#</th>
                            <th width="15%" class="sorting_asc" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-sort="ascending"
                                aria-label="Title: activate to sort column descending">
                                Syllabus
                            </th>
                            <th width="12%" class="sorting_asc" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-sort="ascending"
                                aria-label="Title: activate to sort column descending">
                                Course
                            </th>
                            <th width="15%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending">
                                Class
                            </th>
                            <th width="25%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1"
                                aria-label="Activities: activate to sort column ascending">
                                Class Group
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                aria-label="Activities: activate to sort column ascending">
                                Manage Subscription Months
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                aria-label="Activities: activate to sort column ascending">
                                Manage Subjects
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                aria-label="Activities: activate to sort column ascending">
                                Manage Fee
                            </th>
                            <th width="5%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                aria-label="Activities: activate to sort column ascending">
                                Delete
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr role="row" class="odd" v-for="(group,index) in classGroups">
                            <td>
                                {{ index + 1 }}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{ group.syllabus.syllabus_name }}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{
                                    group.class_groups.user_classes ? group.class_groups.user_classes.course.course_name : '-'
                                }}
                            </td>
                            <td class="v-align-middle">
                                {{ group.class_groups.user_classes ? group.class_groups.user_classes.class_name : '-' }}
                            </td>
                            <td class="v-align-middle">
                                {{ group.class_groups.class_group_name }}
                            </td>
                            <td class="v-align-middle">
                                <button class="btn btn-sm btn-outline-success" @click.prevent="manageMonths(group)">
                                    <i class="fa fa-gear mr-1"></i> Manage Months
                                </button>
                            </td>
                            <td class="v-align-middle">
                                <button class="btn btn-sm btn-outline-success" @click.prevent="manageSubjects(group)">
                                    <i class="fa fa-gear mr-1"></i> Manage Subjects
                                </button>
                            </td>
                            <td class="v-align-middle">
                                <button class="btn btn-sm btn-outline-success" @click.prevent="manageFee(group)">
                                    <i class="fa fa-gear mr-1"></i> Manage Fee
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-sm p-0 btn-danger ml-1"
                                        @click.prevent="deleteClassGroup(group)">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!classGroups.length">
                            <td colspan="7" class="hint-text text-center">
                                <h5 class="font-weight-light">No class group & syllabus combinations available</h5>
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
    name: "ListClassGroupSyllabus",
    props: {
        refreshData: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            classGroups: []
        }
    },
    mounted() {
        this.getAllClassGroups();
    },
    watch: {
        refreshData() {
            if (this.refreshData)
                this.getAllClassGroups()
        }
    },
    methods: {
        manageFee(group) {
            this.$router.push({
                name: "courseFee",
                params: {
                    classGroupSyllabusId: group.class_group_syllabus_id
                }
            });
        },
        getAllClassGroups() {
            $('#syllabus-list').block();
            this.$axios.get(SubjectAPI.GET_ALL_CLASS_GROUPS)
                .then(response => {
                    $('#syllabus-list').unblock();
                    const responseData = response.data;
                    this.$emit("on-refresh-completed");
                    if (responseData.success) {
                        this.classGroups = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    this.$emit("on-refresh-completed");
                    $('#syllabus-list').block();
                    $.showNotification("Error occurred while fetching the class groups", "error");
                })
        },
        manageSubjects(group) {
            this.$router.push({
                name: "designClassSubjects",
                params: {classGroupSyllabusId: group.class_group_syllabus_id}
            })
        },
        manageMonths(group) {
            this.$router.push({
                name: "subscriptionMonths",
                params: {classGroupSyllabusId: group.class_group_syllabus_id}
            })
        },
        deleteClassGroup(classGroupSyllabus) {
            const self = this;
            $.simpleDialog({
                title: "Do you want to delete?",
                message: "Deleting of class group-syllabus is not possible until removing all subjects from the selected class group-syllabus. Do you want to continue?",
                confirmBtnText: "Yes! Delete",
                closeBtnText: "No! Cancel",
                confirmBtnClass: "btn-danger",
                closeBtnClass: "btn-success",
                onSuccess: function () {
                    self.delete(classGroupSyllabus);
                }
            });
        },
        delete(classGroupSyllabus) {
            this.$axios.delete(SubjectAPI.DELETE_CLASS_GROUP_SYLLABUS, {
                params: {
                    "class_group_syllabus_id": classGroupSyllabus.class_group_syllabus_id,
                }
            })
                .then(response => {
                    const responseData = response.data;
                    if (responseData.success) {
                        $.showNotification("class group-syllabus mapping deleted successfully", "success");
                        this.getAllClassGroups();
                    } else {
                        $.showNotification(responseData.error, "error", 10000);
                    }
                })
                .catch(error => {
                    console.log(error);
                    $.showNotification("Error occurred while deleting class group-syllabus. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
