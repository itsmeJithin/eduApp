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
                                Manage Doubts
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr role="row" class="odd" v-for="(subject,index) in subjects">
                            <td>
                                {{ index + 1 }}
                            </td>
                            <td class="v-align-middle semi-bold sorting_1">
                                {{ subject.subject_name }}
                            </td>
                            <td class="v-align-middle">
                                {{ subject.subject_code }}
                            </td>
                            <td class="v-align-middle">
                                {{ subject.subject_description }}
                            </td>
                            <td class="v-align-middle">
                                <button class="btn btn-outline-success" @click.prevent="manageDoubts(subject)">
                                    <i class="fa fa-gear mr-1"></i> Manage Doubts
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
export default {
    name: "ViewClassGroupSubjects",
    props: {
        subjects: {
            default() {
                return []
            }
        }
    },
    methods: {
        manageDoubts(subject) {
            this.$router.push({
                name: "manageDoubts",
                params: {
                    classGroupSyllabusSubjectId: subject.class_group_syllabus_subject_id
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
