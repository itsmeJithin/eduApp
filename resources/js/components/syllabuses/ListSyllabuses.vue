<template>
    <div class="card ">
        <div class="card-header ">
            <div class="card-title">
                Available Syllabuses
            </div>
            <div class="tools">
                <button class="btn btn-success pull-right" @click.prevent="showCreateModal">
                    <i class="fa fa-plus mr-1"/> Create Syllabus
                </button>
            </div>
        </div>
        <div class="card-body" id="syllabus-list">
            <div class="table-responsive">
                <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
                    <table class="table  dataTable no-footer" id="stripedTable" role="grid">
                        <thead>
                        <tr role="row">
                            <th width="5%">#</th>
                            <th width="20%" class="sorting_asc" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-sort="ascending"
                                aria-label="Title: activate to sort column descending">
                                Syllabus Name
                            </th>
                            <th width="20%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1" aria-label="Places: activate to sort column ascending">
                                Start Year
                            </th>
                            <th width="45%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                rowspan="1" colspan="1"
                                aria-label="Activities: activate to sort column ascending">
                                End Year
                            </th>
                            <th width="10%" class="sorting" tabindex="0" aria-controls="stripedTable"
                                aria-label="Activities: activate to sort column ascending">
                                Edit/Delete
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr role="row" class="odd" v-for="(syllabus,index)     in syllabuses">
                            <td>
                                {{index+1}}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{syllabus.syllabus_name}}
                            </td>
                            <td class="v-align-middle">
                                {{syllabus.start_year}}
                            </td>
                            <td class="v-align-middle">
                                {{syllabus.end_year}}
                            </td>
                            <td>
                                <button class="btn btn-sm p-0 btn-default" @click.prevent="updateSyllabus(syllabus)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-sm p-0 btn-danger ml-1"
                                        @click.prevent="deleteSyllabus(syllabus)">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <CreateOrEditSyllabusModal :show-slider="showModal"
                                   :selected-syllabus="selectedSyllabus"
                                   @on-close="onModalClose"/>

    </div>
</template>

<script>
    import SyllabusesAPI from "./SyllabusesAPI";
    import CreateOrEditSyllabusModal from "./CreateOrEditSyllabusModal";

    export default {
        name: "ListSyllabuses",
        components: {
            CreateOrEditSyllabusModal
        },
        data() {
            return {
                syllabuses: [],
                showModal: false,
                selectedSyllabus: null
            }
        },
        mounted() {
            this.getAllSyllabuses();
        },
        methods: {
            showCreateModal() {
                this.showModal = true;
            },
            onModalClose(status) {
                this.showModal = false;
                this.isUpdate = false;
                this.selectedSyllabus = null;
                if (status)
                    this.getAllSyllabuses();
            },
            getAllSyllabuses() {
                $('#syllabus-list').block();
                this.$axios.get(SyllabusesAPI.GET_ALL_SYLLABUSES)
                    .then((response) => {
                        $('#syllabus-list').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.syllabuses = responseData.data;
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#syllabus-list').unblock();
                        $.showNotification("Error occurred while fetching syllabuses. Try again later", "error");
                    })
            },
            updateSyllabus(syllabus) {
                this.selectedSyllabus = syllabus;
                this.showModal = true;
            },
            deleteSyllabus(syllabus) {
                const self = this;
                $.simpleDialog({
                    title: "Do you want to delete?",
                    message: "Deleting of syllabus in not possible until removing all class groups from the syllabuses. Do you want to continue?",
                    confirmBtnText: "Yes! Delete",
                    closeBtnText: "No! Cancel",
                    confirmBtnClass: "btn-danger",
                    closeBtnClass: "btn-success",
                    onSuccess: function () {
                        self.delete(syllabus);
                    }
                });
            },
            delete(syllabus) {
                this.$axios.delete(SyllabusesAPI.DELETE_SYLLABUS, {
                    params: {
                        "syllabus_id": syllabus.syllabus_id
                    }
                })
                    .then(response => {
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification("Syllabus deleted successfully", "success");
                            this.getAllSyllabuses();
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $.showNotification("Error occurred while deleting syllabus. Try again later", "error");
                    })
            }
        }
    }
</script>

<style scoped>

</style>
