<template>
    <SliderModal id="create--demo-topic-modal" :show-slider="showSlider" @on-close="onClose" :options="sliderOptions">
        <template slot="modal-header">
            <h6 class="modal-title mt-0">Create Topic</h6>
        </template>
        <template slot="modal-content">
            <form role="form" class="mt-3">
                <div class="form-group form-group-default required"
                     :class="{ 'has-error': $v.topicName.$error }">
                    <label for="topic-name">Topic Name</label>
                    <input id="topic-name" type="text" class="form-control" required
                           placeholder="Enter a demo topic name" v-model.trim="$v.topicName.$model"/>
                </div>
                <label class="error" role="alert" for="topic-name"
                       v-if="$v.topicName.$error&&!$v.topicName.required">
                    Enter a valid topic name
                </label>
                <label class="error" role="alert" for="topic-name"
                       v-if="!$v.topicName.minLength">
                    Subject name must have at least {{ $v.topicName.$params.minLength.min }}
                    letters.
                </label>
                <div class="form-group form-group-default required"
                     :class="{ 'has-error': $v.topicVideoUrl.$error }">
                    <label for="topic-url">Topic Video URL</label>
                    <input id="topic-url" type="text" class="form-control" required
                           placeholder="Enter a demo topic url" v-model.trim="$v.topicVideoUrl.$model"/>
                </div>
                <label class="error" role="alert" for="topic-url"
                       v-if="$v.topicVideoUrl.$error&&!$v.topicVideoUrl.required">
                    Enter a valid topic url
                </label>
                <label class="error" role="alert" for="topic-url"
                       v-if="!$v.topicVideoUrl.validateYoutubeURL">
                    Invalid youtube video link given
                </label>
                <label class="error" role="alert" for="topic-url"
                       v-if="errors&&errors.topic_url&&errors.topic_url.length">
                    {{ errors.topic_url[0] }}
                </label>
                <div class="form-group form-group-default">
                    <label for="topic-description">Topic Description</label>
                    <textarea id="topic-description" name="subject_description"
                              cols="30" v-model.trim="topicDescription"
                              class="form-control height-3rem" placeholder="Enter topic description"
                              rows="10">
                            </textarea>
                </div>
            </form>
        </template>
        <template slot="modal-footer">
            <div class="form-group text-right mt-2">
                <button class="btn btn-default mr-1" @click.prevent="onClose(false)">
                    <i class="fa fa-times mr-1"></i> Close
                </button>
                <button class="btn btn-success" @click.prevent="saveTopicDetails">
                    <i class="fa fa-save mr-1"></i> Save
                </button>
            </div>
        </template>
    </SliderModal>
</template>

<script>
import SliderModal from "../common/SliderModal";
import {minLength, required} from "vuelidate/lib/validators";
import TopicsAPI from "../topics/TopicsAPI";
import DemoTopicAPIs from "./DemoTopicAPIs";

const validateYoutubeURL = (value) => {
    if (!value)
        return true;
    var p = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
    if (value.match(p)) {
        return value.match(p)[1];
    }
    return false;
};
export default {
    name: "CreateDemoTopic",
    components: {
        SliderModal
    },
    props: {
        showSlider: {
            required: true,
            default: false,
            type: Boolean
        },
        selectedSubject: {
            default: () => {
                return null
            }
        }
    },
    data() {
        return {
            sliderOptions: {
                position: "top",
                size: "medium"
            },
            topicName: "",
            topicCode: "",
            topicDescription: "",
            topicVideoUrl: "",
            errors: null,
            demoTopicId: null,
        }
    },
    validations: {
        topicName: {
            required,
            minLength: minLength(3)
        },
        topicVideoUrl: {
            required,
            validateYoutubeURL
        }
    },
    watch: {
        selectedSubject() {
            if (this.selectedSubject) {
                this.getDemoTopicBySubject();
            }
        }
    },
    methods: {
        reset() {
            this.demoTopicId = "";
            this.topicDescription = "";
            this.topicName = "";
            this.topicVideoUrl = "";
            this.$v.$reset();
        },
        onClose() {
            this.reset();
            this.$emit("on-close")
        },
        getDemoTopicBySubject() {
            $('#create--demo-topic-modal').find(".modal-dialog").block();
            this.$axios.get(DemoTopicAPIs.GET_SUBJECT_DEMO_TOPIC, {
                params: {
                    class_group_syllabus_subject_id: this.selectedSubject.class_group_syllabus_subject_id
                }
            })
                .then(response => {
                    $('#create--demo-topic-modal').find(".modal-dialog").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        if (responseData.data) {
                            this.topicName = responseData.data.demo_topic_name;
                            this.topicDescription = responseData.data.description;
                            this.topicVideoUrl = responseData.data.demo_video_url;
                            this.demoTopicId = responseData.data.ss_month_demo_topic_id;
                        }
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#create--demo-topic-modal').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while fetching the demo topic details. Try again later", "error");
                })
        },
        saveTopicDetails() {
            this.$v.$touch();
            if (this.$v.$invalid) {
                $.showNotification("Fill all required fields with valid data", "error");
                return
            }
            const data = {
                "topic_name": this.topicName,
                "topic_description": this.topicDescription,
                "class_group_syllabus_subject_id": this.selectedSubject.class_group_syllabus_subject_id,
                "video_url": this.topicVideoUrl,
                "ss_month_demo_topic_id": this.demoTopicId
            };
            $('#create--demo-topic-modal').find(".modal-dialog").block();
            this.$axios.post(DemoTopicAPIs.CREATE_OR_UPDATE_DEMO_TOPICS, data)
                .then(response => {
                    $('#create--demo-topic-modal').find(".modal-dialog").unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        $.showNotification("Demo topic details saved successfully", "success");
                        this.onClose();
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#create--demo-topic-modal').find(".modal-dialog").unblock();
                    $.showNotification("Error occurred while saving demo topic details. Try again later", "error");
                })
        }
    }
}
</script>

<style scoped>

</style>
