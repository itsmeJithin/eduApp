<template>
    <div class="card " id="exam-modes">
        <div class="card-header ">
            <div class="card-title">
                Available Exam Modes
            </div>
            <div class="tools">
                <button class="btn btn-success pull-right" @click.prevent="showCreateModal">
                    <i class="fa fa-plus mr-1"/> Create Exam Mode
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
                            <th width="20%">
                                Class Name
                            </th>
                            <th width="20%">
                                Class Code
                            </th>
                            <th width="25%">
                                Description
                            </th>
                            <th width="10%">
                                Edit/Delete
                            </th>
                        </tr>
                        </thead>
                        <tbody v-if="examModes.length">

                        <tr role="row" class="odd" v-for="(mode,index) in examModes">
                            <td>
                                {{index+1}}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{mode.exam_mode_name}}
                            </td>
                            <td class="v-align-middle">
                                {{mode.exam_mode_code}}
                            </td>
                            <td class="v-align-middle">
                                {{mode.exam_mode_description}}
                            </td>
                            <td>
                                <button class="btn btn-sm p-0 btn-default" @click.prevent="updateExamMode(mode)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 btn-danger ml-1"
                                        @click.prevent="deleteExamMode(mode)">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot v-else>
                        <tr>
                            <td colspan="5" class="hint-text text-center">
                                <h4 class="font-weight-lighter">You have not created any exam modes.</h4>
                                Click on the <code class="text-success">Create Exam mode</code> button to create a
                                new exam modes
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <CreateOrEditExamModes :show-slider="showModal"
                               :selected-mode="selectedMode"
                               @on-close="onModalClose"/>

    </div>
</template>

<script>
    import CreateOrEditExamModes from "./CreateOrEditExamModes";
    import ExamModesAPI from "./ExamModesAPI";

    export default {
        name: "ManageExamModes",
        components: {CreateOrEditExamModes},
        data() {
            return {
                selectedMode: null,
                showModal: false,
                examModes: []
            }
        },
        mounted() {
            this.getAllExamModes();
        },
        methods: {
            showCreateModal() {
                this.showModal = true;
            },
            onModalClose(status) {
                this.showModal = false;
                if (status)
                    this.getAllExamModes();
            },
            updateExamMode(mode) {
                this.selectedMode = mode;
                this.showModal = true;
            },
            deleteExamMode(mode) {

            },
            getAllExamModes() {
                $('#exam-modes').block();
                this.$axios.get(ExamModesAPI.GET_ALL_EXAM_MODES)
                    .then(response => {
                        $('#exam-modes').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.examModes = responseData.data;
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#exam-modes').unblock();
                        $.showNotification("Error occurred while fetching the exam modes", "error");
                    })
            }
        }
    }
</script>

<style scoped>

</style>
