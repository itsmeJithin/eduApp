<template>
    <div class="card ">
        <div class="card-header ">
            <div class="card-title">
                Available Class Groups
            </div>
            <div class="tools">
                <button class="btn btn-success pull-right" @click.prevent="showCreateModal">
                    <i class="fa fa-plus mr-1"/> Create Class Group
                </button>
            </div>
        </div>
        <div class="card-body" id="class-list">
            <div class="table-responsive">
                <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
                    <table class="table  dataTable no-footer" id="stripedTable" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="5%">#</th>
                            <th width="15%" class="sorting_asc" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-sort="ascending"
                                aria-label="Title: activate to sort column descending">Group Name
                            </th>
                            <th width="15%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending">
                                Group Code
                            </th>
                            <th width="20%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1"
                                aria-label="Activities: activate to sort column ascending">Description
                            </th>
                            <th width="20%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1"
                                aria-label="Activities: activate to sort column ascending">Class
                            </th>
                            <th width="15%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1"
                                aria-label="Activities: activate to sort column ascending">Course
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                aria-label="Activities: activate to sort column ascending">
                                Edit/Delete
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr role="row" class="odd" v-for="(group,index) in classGroups">
                            <td>
                                {{ index + 1 }}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{ group.class_group_name }}
                            </td>
                            <td class="v-align-middle">
                                {{ group.class_group_code }}
                            </td>
                            <td class="v-align-middle">
                                {{ group.class_group_description }}
                            </td>
                            <td class="v-align-middle">
                                {{ group.user_classes ? group.user_classes.class_name : '' }}
                            </td>
                            <td class="v-align-middle">
                                {{ group.user_classes ? group.user_classes.course.course_name : '' }}
                            </td>
                            <td>
                                <button class="btn btn-sm p-0 btn-default" @click.prevent="updateClassGroup(group)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 btn-danger ml-1"
                                        @click.prevent="deleteClassGroup(group)">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <CreateOrEditClassGroupModal :show-slider="showModal"
                                     :selected-class-group="selectedClassGroup"
                                     @on-close="onModalClose"/>

    </div>
</template>

<script>
import CreateOrEditClassGroupModal from "./CreateOrEditClassGroupModal";
import ClassGroupAPI from "./ClassGroupAPI";

export default {
    name: "ListClassGroups",
    components: {
        CreateOrEditClassGroupModal
    },
    data() {
        return {
            classGroups: [],
            showModal: false,
            selectedClassGroup: null
        }
    },
    mounted() {
        this.getAllClasses();
    },
    methods: {
        showCreateModal() {
            this.showModal = true;
        },
        onModalClose(status) {
            this.showModal = false;
            this.isUpdate = false;
            this.selectedClassGroup = null;
            if (status)
                this.getAllClasses();
        },
        getAllClasses() {
            $('#class-list').block();
            this.$axios.get(ClassGroupAPI.GET_ALL_CLASS_GROUPS)
                .then((response) => {
                    $('#class-list').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.classGroups = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#class-list').unblock();
                    $.showNotification("Error occurred while fetching the class groups", "error");
                })
        },
        updateClassGroup(group) {
            this.selectedClassGroup = group;
            this.showModal = true;
        },
        deleteClassGroup(group) {
            const self = this;
            $.simpleDialog({
                title: "Do you want to delete?",
                message: "Deleting of course in not possible until removing all classes from the course. Do you want to continue?",
                confirmBtnText: "Yes! Delete",
                closeBtnText: "No! Cancel",
                confirmBtnClass: "btn-danger",
                closeBtnClass: "btn-success",
                onSuccess: function () {
                    self.delete(group);
                }
            });
        },
        delete(group) {
            $('#class-list').block();
            this.$axios.delete(ClassGroupAPI.DELETE_CLASS_GROUP, {
                params: {
                    "class_group_id": group.class_group_id
                }
            })
                .then(response => {
                    $('#class-list').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        $.showNotification("Class group deleted successfully", "success");
                        this.getAllClasses();
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#class-list').unblock();
                    $.showNotification("Error occurred while deleting class group. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
