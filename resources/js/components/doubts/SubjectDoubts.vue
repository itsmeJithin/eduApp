<template>
    <div class="tab-pane sm-no-padding slide-left active" id="tab22">
        <h4 class="card-heading mt-2 mb-2">Available Doubts</h4>
        <div class="row">
            <div class="col-md-6" v-for="(doubt,index) in doubts">
                <div class="card card-bordered">
                    <div class="card-body no-padding">
                        <div id="card-advance" class="card card-default">
                            <div class="card-header  ">
                                <div class="card-title">
                                    <div class="profile-img-wrapper inline">
                                        <img width="35" height="35"
                                             :data-src-retina="formatImageUrl(doubt)"
                                             :data-src="formatImageUrl(doubt)" alt=""
                                             :src="formatImageUrl(doubt)">
                                        <div class="chat-status available">
                                        </div>
                                    </div>
                                    <div class="inline m-l-10">
                                        <p class="small card-heading mt-0-7">
                                            {{ doubt.user_name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="card-controls">
                                    <span class="card-heading mt-0-8rem">#{{ index + 1 }}</span>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                <p>
                                    {{ doubt.doubt }}
                                </p>
                                <p>
                                    <span class="font-weight-bold">Answer: </span>

                                    <span class="hint-text">
                                        {{ doubt.answer ? doubt.answer : '-' }}
                                    </span>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Answered By: </span>
                                    <span class="hint-text">
                                        {{ doubt.answer ? doubt.staff_name : '-' }}
                                    </span>
                                </p>
                                <button class="btn btn-outline-success pull-right mt-3" type="button"
                                        v-if="!doubt.answer" @click.prevent="showSlider(doubt)">
                                    <i class="fa fa-pencil mr-1"></i>Answer Now
                                </button>
                                <button class="btn btn-outline-danger pull-right mt-3 mr-1" type="button">
                                    <i class="fa fa-trash mr-1"></i>Delete Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <AnswerDoubtsModal :selected-doubt="selectedDoubt"
                           :show-slider="showAnswerDoubtSlider"
                           @on-close="closeSlider"/>
    </div>
</template>

<script>
import DoubtsAPIs from "./DoubtsAPIs";
import AnswerDoubtsModal from "./AnswerDoubtsModal";

export default {
    name: "SubjectDoubts",
    components: {AnswerDoubtsModal},
    props: {
        classGroupSyllabusSubjectId: {
            required: true,
        }
    },
    data() {
        return {
            doubts: [],
            selectedDoubt: null,
            showAnswerDoubtSlider: false
        }
    },
    mounted() {
        if (this.classGroupSyllabusSubjectId) {
            this.getAllSubjectDoubts();
        }
    },
    watch: {
        classGroupSyllabusSubjectId() {
            if (this.classGroupSyllabusSubjectId) {
                this.getAllSubjectDoubts();
            }
        }
    },
    methods: {
        showSlider(doubt) {
            this.showAnswerDoubtSlider = true;
            this.selectedDoubt = doubt;
        },
        closeSlider(status) {
            this.showAnswerDoubtSlider = false;
            this.selectedDoubt = null;
            if (status) {
                this.getAllSubjectDoubts();
            }
        },
        formatImageUrl(value) {
            if (value.avatar === "avatar.png")
                return "/storage/avatars/" + value.user_id + "/avatar.png"
            return value.avatar;
        },
        getAllSubjectDoubts() {
            this.$axios.post(DoubtsAPIs.GET_ALL_SUBJECT_DOUBTS, {
                "class_group_syllabus_subject_id": this.classGroupSyllabusSubjectId
            })
                .then(response => {
                    const responseData = response.data;
                    if (responseData.success) {
                        this.doubts = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $.showNotification("Error occurred while fetching the subject doubts. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
