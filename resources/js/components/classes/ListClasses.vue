<template>
    <div class="card ">
        <div class="card-header ">
            <div class="card-title">
                Available Classes
            </div>
            <div class="tools">
                <button class="btn btn-success pull-right" @click.prevent="showCreateModal">
                    <i class="fa fa-plus mr-1"/> Create Classes
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
                            <th width="20%" class="sorting_asc" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-sort="ascending"
                                aria-label="Title: activate to sort column descending">Class Name
                            </th>
                            <th width="20%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending">
                                Class Code
                            </th>
                            <th width="25%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1"
                                aria-label="Activities: activate to sort column ascending">Description
                            </th>
                            <th width="20%" class="sorting" tabindex="0" aria-controls="stripedTable"
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

                        <tr role="row" class="odd" v-for="(courseClass,index) in classes">
                            <td>
                                {{index+1}}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{courseClass.class_name}}
                            </td>
                            <td class="v-align-middle">
                                {{courseClass.class_code}}
                            </td>
                            <td class="v-align-middle">
                                {{courseClass.class_description}}
                            </td>
                            <td class="v-align-middle">
                                {{courseClass.course.course_name}}
                            </td>
                            <td>
                                <button class="btn btn-sm p-0 btn-default" @click.prevent="updateClass(courseClass)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 btn-danger ml-1"
                                        @click.prevent="deleteClass(courseClass)">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <CreateOrEditClassModal :show-slider="showModal"
                                :selected-class="selectedClass"
                                @on-close="onModalClose"/>

    </div>
</template>

<script>
    import CreateOrEditClassModal from "./CreateOrEditClassModal";
    import ClassesAPI from "./ClassesAPI";

    export default {
        name: "ListCourses",
        components: {
            CreateOrEditClassModal
        },
        data() {
            return {
                classes: [],
                showModal: false,
                selectedClass: null
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
                this.selectedClass = null;
                if (status)
                    this.getAllClasses();
            },
            getAllClasses() {
                $('#class-list').block();
                this.$axios.get(ClassesAPI.GET_ALL_CLASSES)
                    .then((response) => {
                        $('#class-list').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.classes = responseData.data;
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#class-list').unblock();
                        $.showNotification("Error occurred while fetching the classes", "error");
                    })
            },
            updateClass(courseClass) {
                this.selectedClass = courseClass;
                this.showModal = true;
            },
            deleteClass(courseClass) {
                const self = this;
                $.simpleDialog({
                    title: "Do you want to delete?",
                    message: "Deleting of course in not possible until removing all classes from the course. Do you want to continue?",
                    confirmBtnText: "Yes! Delete",
                    closeBtnText: "No! Cancel",
                    confirmBtnClass: "btn-danger",
                    closeBtnClass: "btn-success",
                    onSuccess: function () {
                        self.delete(courseClass);
                    }
                });
            },
            delete(courseClass) {
                $('#class-list').block();
                this.$axios.delete(ClassesAPI.DELETE_CLASS, {
                    params: {
                        "class_id": courseClass.class_id
                    }
                })
                    .then(response => {
                        $('#class-list').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification("Class deleted successfully", "success");
                            this.getAllClasses();
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#class-list').unblock();
                        $.showNotification("Error occurred while deleting classes. Try again later", "error");
                    })
            }
        }
    }
</script>

<style scoped>

</style>
