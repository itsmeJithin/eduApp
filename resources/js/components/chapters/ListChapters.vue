<template>
    <div class="card ">
        <div class="card-header ">
            <div class="card-title">
                Assigned Chapters
            </div>
            <div class="tools">
            </div>
        </div>
        <div class="card-body" id="chapter-list">
            <div class="table-responsive">
                <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
                    <table class="table  dataTable no-footer" id="stripedTable" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="5%">#</th>
                            <th width="20%">
                                Chapter Name
                            </th>
                            <th width="20%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                Chapter Code
                            </th>
                            <th width="45%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                Chapter Description
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                Manage Topics
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                Edit/Delete
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr role="row" class="odd" v-for="(chapter,index) in chapters">
                            <td>
                                {{index+1}}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{chapter.chapter_name}}
                            </td>
                            <td class="v-align-middle">
                                {{chapter.chapter_code}}
                            </td>
                            <td class="v-align-middle">
                                {{chapter.chapter_description}}
                            </td>
                            <td class="v-align-middle">
                                <button class="btn btn-outline-success" @click.prevent="manageTopics(chapter)">
                                    <i class="fa fa-plus mr-1"></i> Manage Topics
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-sm p-0 btn-default" @click.prevent="updateChapter(chapter)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 btn-danger ml-1"
                                        @click.prevent="deleteChapter(chapter)">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!chapters.length">
                            <td colspan="6" class="hint-text text-center">
                                <h5 class="font-weight-light">No chapters assigned to this subjects</h5>
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
    import ChaptersAPI from "./ChaptersAPI";
    import SubjectAPI from "../subjects/SubjectAPI";

    export default {
        name: "ListChapters",
        props: {
            classGroupSyllabusId: {
                required: true,
                type: String
            },
            subjectId: {
                required: true,
                type: String
            },
            fetchChaptersList: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                chapters: []
            }
        },
        mounted() {
            this.getAllChapters();
        },
        watch: {
            fetchChaptersList() {
                if (this.fetchChaptersList)
                    this.getAllChapters();
            }
        },
        methods: {
            getAllChapters() {
                $('#chapter-list').block();
                this.$axios.get(ChaptersAPI.GET_ALL_CHAPTERS, {
                    params: {
                        class_group_syllabus_id: this.classGroupSyllabusId,
                        subject_id: this.subjectId
                    }
                })
                    .then(response => {
                        $('#chapter-list').unblock();
                        const responseData = response.data;
                        if (responseData.success)
                            this.chapters = responseData.data.chapters;
                        else
                            $.showNotification(responseData.error, "error")
                    })
                    .catch(() => {
                        $('#chapter-list').unblock();
                        $.showNotification("Error occurred while fetching the chapters", "error");
                    })
            },
            manageTopics(chapter) {
                this.$router.push({
                    name: "designClassSubjects",
                    params: {
                        classGroupSyllabusId: this.classGroupSyllabusId,
                        subjectId: this.subjectId,
                        chapterId: chapter.chapter_id
                    }
                })
            },
            updateChapter(chapter) {
                this.$emit("on-edit-chapter", chapter);
            },
            deleteChapter(chapter) {
                const self = this;
                $.simpleDialog({
                    title: "Do you want to delete?",
                    message: "Deleting of chapter is not possible until removing all topics from the subjects. Do you want to continue?",
                    confirmBtnText: "Yes! Delete",
                    closeBtnText: "No! Cancel",
                    confirmBtnClass: "btn-danger",
                    closeBtnClass: "btn-success",
                    onSuccess: function () {
                        self.delete(chapter);
                    }
                });
            },
            delete(chapter) {
                $('#chapter-list').block();
                this.$axios.delete(ChaptersAPI.DELETE_CHAPTER, {
                    params: {
                        "chapter_id": chapter.chapter_id
                    }
                })
                    .then(response => {
                        $('#chapter-list').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification("Chapter deleted successfully", "success");
                            this.getAllChapters();
                        } else {
                            $.showNotification(responseData.error, "error",10000);
                        }
                    })
                    .catch(() => {
                        $('#chapter-list').unblock();
                        $.showNotification("Error occurred while deleting chapter. Try again later", "error");
                    })
            }

        }
    }
</script>

<style scoped>

</style>
