<template>
    <div class="card ">
        <div class="card-header ">
            <div class="card-title">
                Available Courses
            </div>
            <div class="tools">
                <button class="btn btn-success pull-right" @click.prevent="showCreateModal">
                    <i class="fa fa-plus mr-1"/> Create Course
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
                    <table class="table  dataTable no-footer" id="stripedTable" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="5%">#</th>
                            <th width="20%" class="sorting_asc" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-sort="ascending"
                                aria-label="Title: activate to sort column descending">Course Name
                            </th>
                            <th width="20%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending">
                                Course Code
                            </th>
                            <th width="45%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1"
                                aria-label="Activities: activate to sort column ascending">Description
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                aria-label="Activities: activate to sort column ascending">
                                Edit/Delete
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr role="row" class="odd" v-for="(course,index)     in courses">
                            <td>
                                {{index+1}}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{course.course_name}}
                            </td>
                            <td class="v-align-middle">
                                {{course.course_code}}
                            </td>
                            <td class="v-align-middle">
                                {{course.course_description}}
                            </td>
                            <td>
                                <button class="btn btn-sm p-0 btn-default" @click.prevent="updateCourse(course)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 btn-danger ml-1" @click.prevent="deleteCourse(course)">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <CreateOrEditCourseModal :show-slider="showModal"
                                 :selected-course="selectedCourse"
                                 @on-close="onModalClose"/>

    </div>
</template>

<script>
    import CreateOrEditCourseModal from "./CreateOrEditCourseModal";
    import CourseAPIs from "./CourseAPIs";

    export default {
        name: "ListCourses",
        components: {
            CreateOrEditCourseModal
        },
        data() {
            return {
                courses: [],
                showModal: false,
                selectedCourse: null
            }
        },
        mounted() {
            this.getAllCourses();
        },
        methods: {
            showCreateModal() {
                this.showModal = true;
            },
            onModalClose(status) {
                this.showModal = false;
                this.isUpdate = false;
                this.selectedCourse = null;
                if (status)
                    this.getAllCourses();
            },
            getAllCourses() {
                this.$axios.get(CourseAPIs.GET_ALL_COURSES)
                    .then((response) => {
                        console.log(response);
                        const responseData = response.data;
                        if (responseData.success) {
                            this.courses = responseData.data;
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $.showNotification("Error occurred while fetching courses. Try again later", "error");
                    })
            },
            updateCourse(course) {
                this.selectedCourse = course;
                this.showModal = true;
            },
            deleteCourse(course) {
                const self = this;
                $.simpleDialog({
                    title: "Do you want to delete?",
                    message: "Deleting of course in not possible until removing all classes from the course. Do you want to continue?",
                    confirmBtnText: "Yes! Delete",
                    closeBtnText: "No! Cancel",
                    confirmBtnClass: "btn-danger",
                    closeBtnClass: "btn-success",
                    onSuccess: function () {
                        self.delete(course);
                    }
                });
            },
            delete(course) {
                this.$axios.delete(CourseAPIs.DELETE_COURSE, {
                    params: {
                        "course_id": course.course_id
                    }
                })
                    .then(response => {
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification("Course deleted successfully", "success");
                            this.getAllCourses();
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $.showNotification("Error occurred while deleting courses. Try again later", "error");
                    })
            }
        }
    }
</script>

<style scoped>

</style>
