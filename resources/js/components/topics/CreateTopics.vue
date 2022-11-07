<template>
    <SliderModal id="create-topic-modal" :show-slider="showSlider" @on-close="onClose" :options="sliderOptions">
        <template slot="modal-header">
            <h6 class="modal-title mt-0">Create Topic</h6>
        </template>
        <template slot="modal-content">
            <form role="form" class="mt-3">
                <div class="form-group form-group-default required"
                     :class="{ 'has-error': $v.topicName.$error }">
                    <label for="topic-name">Topic Name</label>
                    <input id="topic-name" type="text" class="form-control" required
                           placeholder="Enter a unique topic name" v-model.trim="$v.topicName.$model"/>
                </div>
                <label class="error" role="alert" for="topic-name"
                       v-if="$v.topicName.$error&&!$v.topicName.required">
                    Enter a valid topic name
                </label>
                <label class="error" role="alert" for="topic-name"
                       v-if="!$v.topicName.minLength">
                    Subject name must have at least {{$v.topicName.$params.minLength.min}}
                    letters.
                </label>
                <div class="form-group form-group-default required"
                     :class="{ 'has-error': $v.topicCode.$error }">
                    <label for="topic-code">Topic Code</label>
                    <input id="topic-code" type="text" class="form-control" required
                           placeholder="Enter a unique topic code" v-model.trim="$v.topicCode.$model"/>
                </div>
                <label class="error" role="alert" for="topic-code"
                       v-if="$v.topicCode.$error&&!$v.topicCode.required">
                    Enter a valid topic code
                </label>
                <label class="error" role="alert" for="topic-code"
                       v-if="!$v.topicCode.minLength">
                    Topic code must have at least {{$v.topicCode.$params.minLength.min}}
                    letters.
                </label>
                <label class="error" role="alert" for="topic-code"
                       v-if="errors&&errors.topic_code&&errors.topic_code.length">
                    {{errors.topic_code[0]}}
                </label>

                <div class="form-group form-group-default required"
                     :class="{ 'has-error': $v.topicVideoUrl.$error }">
                    <label for="topic-url">Topic Video URL</label>
                    <input id="topic-url" type="text" class="form-control" required
                           placeholder="Enter a unique topic url" v-model.trim="$v.topicVideoUrl.$model"/>
                </div>
                <label class="error" role="alert" for="topic-url"
                       v-if="$v.topicVideoUrl.$error&&!$v.topicVideoUrl.required">
                    Enter a valid topic url
                </label>
                <label class="error" role="alert" for="topic-code"
                       v-if="!$v.topicVideoUrl.validateYoutubeURL">
                    Invalid youtube video link given
                </label>
                <label class="error" role="alert" for="topic-code"
                       v-if="errors&&errors.topic_url&&errors.topic_url.length">
                    {{errors.topic_url[0]}}
                </label>
                <div class="form-group form-group-default">
                    <label for="topic-description">Topic Description</label>
                    <textarea id="topic-description" name="subject_description"
                              cols="30" v-model.trim="topicDescription"
                              class="form-control height-3rem" placeholder="Enter topic description"
                              rows="10">
                            </textarea>
                </div>
                <div v-if="previouslyUploadedStudyMaterials.length">
                    <label>Previously Added Study Materials</label>
                    <div class="card card-default draggable-card  mb-1 card-bordered"
                         v-for="(material,index) in previouslyUploadedStudyMaterials">
                        <div class="card-header pt-2 pb-1">
                            <div class="card-title">
                                <span class="font-weight-bold mr-1">
                                    #{{index+1}}
                                </span>
                                <a class="text-black p-0" :href="material.study_material_url" target="_blank">
                                    {{material.study_material_name}}
                                </a>
                            </div>
                            <div class="card-controls">
                                <a href="" class="p-0" @click.prevent="deleteStudyMaterial(material)">
                                    <i class="fa fa-trash-o text-danger"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <label>Add Study Material</label>
                <Gallery :uploader="uploader"
                         :thumbnail-max-size="100"
                         v-if="conf&&showSlider"/>

            </form>
        </template>
        <template slot="modal-footer">
            <div class="form-group text-right mt-2">
                <button class="btn btn-default mr-1" @click.prevent="onClose(false)" :disabled="isS3UploadRunning">
                    <i class="fa fa-times mr-1"></i> Close
                </button>
                <button class="btn btn-success" @click.prevent="saveTopicDetails" :disabled="isS3UploadRunning">
                    <i class="fa fa-save mr-1"></i> Save
                </button>
            </div>
        </template>
    </SliderModal>
</template>

<script>
    import FineUploaderS3 from 'fine-uploader-wrappers/s3';
    import Gallery from 'vue-fineuploader/gallery'
    import SliderModal from "../common/SliderModal";
    import {minLength, required} from "vuelidate/lib/validators";
    import TopicsAPI from "./TopicsAPI";

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
        name: "CreateTopics",
        components: {
            SliderModal,
            Gallery
        },
        props: {
            showSlider: {
                required: true,
                default: false,
                type: Boolean
            },
            selectedTopic: {
                default: () => {
                    return null
                }
            },
            classGroupSyllabusId: {
                required: true,
                type: String
            },
            subjectId: {
                required: true,
                type: String
            },
            chapterId: {
                required: true,
                type: String
            },
            subscriptionMonthId: {
                required: true,
                type: String
            }

        },
        data() {
            return {
                sliderOptions: {
                    position: "right",
                    size: "large"
                },
                topicName: "",
                topicCode: "",
                topicDescription: "",
                topicVideoUrl: "",
                errors: null,
                uploader: null,
                conf: null,
                isS3UploadRunning: false,
                uploadedMaterials: [],
                previouslyUploadedStudyMaterials: []
            }
        },
        validations: {
            topicName: {
                required,
                minLength: minLength(3)
            },
            topicCode: {
                required,
                minLength: minLength(3)
            },
            topicVideoUrl: {
                required,
                validateYoutubeURL
            }
        },
        watch: {
            selectedTopic() {
                if (this.selectedTopic) {
                    this.getStudyMaterials();
                    this.topicCode = _.clone(this.selectedTopic.topic_code);
                    this.topicName = _.clone(this.selectedTopic.topic_name);
                    this.topicDescription = _.clone(this.selectedTopic.topic_description);
                    this.topicVideoUrl = _.clone(this.selectedTopic.video_url);
                }
            }
        },
        beforeMount() {
            this.getConf();
        },
        methods: {
            reset() {
                this.topicName = "";
                this.topicCode = "";
                this.topicDescription = "";
                this.topicVideoUrl = "";
                this.uploadedMaterials = [];
                this.previouslyUploadedStudyMaterials = [];
                this.$v.$reset();
            },
            onClose(status) {
                this.reset();
                this.$emit("on-close", status)
            },
            getStudyMaterials() {
                $('#create-topic-modal').block();
                this.$axios.get(TopicsAPI.GET_STUDY_MATERIALS, {
                    params: {
                        topic_id: this.selectedTopic.topic_id
                    }
                })
                    .then(response => {
                        $('#create-topic-modal').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.previouslyUploadedStudyMaterials = responseData.data;
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#create-topic-modal').unblock();
                        $.showNotification("Error occurred while fetching the study materials", "error");
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
                    "topic_code": this.topicCode,
                    "topic_description": this.topicDescription,
                    "class_group_syllabus_id": this.classGroupSyllabusId,
                    "subject_id": this.subjectId,
                    "chapter_id": this.chapterId,
                    "video_url": this.topicVideoUrl,
                    "subscription_month_id": this.subscriptionMonthId,
                    "study_materials": JSON.stringify(this.uploadedMaterials)
                };
                if (this.selectedTopic) {
                    data.topic_id = this.selectedTopic.topic_id
                }
                $('#create-topic-modal').block();
                this.$axios.post(TopicsAPI.CREATE_OR_UPDATE_TOPICS, data)
                    .then(response => {
                        $('#create-topic-modal').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification("Topics details saved successfully", "success");
                            this.onClose(true);
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#create-topic-modal').unblock();
                        $.showNotification("Error occurred while saving topics details. Try again later", "error");
                    })
            },
            deleteStudyMaterial(material) {
                const self = this;
                $.simpleDialog({
                    title: "Do you want to delete?",
                    message: "This study material will be permanently deleted. Do you want to continue?",
                    confirmBtnText: "Yes! Delete",
                    closeBtnText: "No! Cancel",
                    confirmBtnClass: "btn-danger",
                    closeBtnClass: "btn-success",
                    onSuccess: function () {
                        self.deleteMaterial(material);
                    }
                });
            },
            deleteMaterial(material) {
                $('#create-topic-modal').block();
                this.$axios.delete(TopicsAPI.DELETE_STUDY_MATERIAL, {
                    params: {
                        study_material_id: material.study_material_id
                    }
                })
                    .then(response => {
                        $('#create-topic-modal').unblock();
                        const data = response.data;
                        if (data.success) {
                            let materials = _.cloneDeep(this.previouslyUploadedStudyMaterials);
                            _.remove(materials, {study_material_id: material.study_material_id});
                            this.previouslyUploadedStudyMaterials = _.cloneDeep(materials);
                            $.showNotification("Study materials removed successfully", "success");
                        } else {
                            $.showNotification(data.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#create-topic-modal').unblock();
                        $.showNotification("Error occurred while deleting study materials", "error");
                    })
            },
            getConf() {
                if (!this.conf)
                    this.$axios.get(TopicsAPI.GET_CONF)
                        .then(response => {
                            const responseData = response.data;
                            if (responseData.success) {
                                this.initS3Conf(responseData.data);
                                this.conf = responseData.data;
                            } else {
                                $.showNotification(responseData.error, "error");
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                            $.showNotification("Error occurred while fetching the configurations. Try again later", "error");
                        });
                else
                    this.initS3Conf(this.conf);
            },
            initS3Conf(conf) {
                const self = this;
                this.uploader = new FineUploaderS3({
                    options: {
                        debug: true,
                        multiple: false,
                        validation: {
                            allowedExtensions: ['pdf'],
                            sizeLimit: conf.sizeLimit
                        },
                        deleteFile: {
                            enabled: true,
                            endpoint: TopicsAPI.DELETE_FILE,
                            customHeaders: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-Token": $("meta[name='csrf-token']").attr("content")
                            }
                        },
                        request: {
                            endpoint: `${conf.serverProtocol}://${conf.bucketName}.s3.amazonaws.com`,
                            accessKey: conf.accessKey

                        },
                        signature: {
                            endpoint: TopicsAPI.SIGN_UPLOAD_REQUEST,
                            version: 4,
                            customHeaders: {
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-Token": $("meta[name='csrf-token']").attr("content")
                            }
                        },
                        objectProperties: {
                            region: conf.region,
                            key: function (fileId) {
                                const filename = self.uploader.methods.getName(fileId);
                                const uuid = self.uploader.methods.getUuid(fileId);
                                const ext = filename.substr(filename.lastIndexOf('.') + 1);
                                const date = new Date();
                                const year = date.getFullYear();
                                return `topic-study-materials/${year}/${uuid}.${ext}`;
                            }
                        },
                        callbacks: {
                            onSubmitted: () => {
                                self.isS3UploadRunning = true;
                            },
                            onError: (id, name, errorReason) => {
                                if (errorReason.toLowerCase().indexOf('policy expired') !== -1) {
                                    $.simpleDialog({
                                        title: "Incorrect System Time",
                                        message: "Your system date and/or time is incorrect. Please correct it and click retry to upload the file."
                                    });
                                }
                            },
                            onDelete() {
                                self.isS3UploadRunning = true;
                            },
                            onComplete: (fileId, name, response) => {
                                if (response.success) {
                                    const fileInfo = {
                                        name: name,
                                        key: self.uploader.methods.getKey(fileId),
                                        fileId: fileId,
                                        bucket: self.uploader.methods.getBucket(fileId)
                                    };
                                    self.uploadedMaterials.push(fileInfo);
                                    console.log(this.uploadedMaterials);
                                }
                            },
                            onAllComplete: () => {
                                self.isS3UploadRunning = false;
                            },
                            onDeleteComplete: (fileId, xhr, isError) => {
                                if (!isError) {
                                    const files = this.uploadedMaterials;
                                    _.remove(files, {"fileId": fileId});

                                    this.uploadedMaterials = files;
                                    console.log(this.uploadedMaterials);
                                }
                                self.isS3UploadRunning = false;
                            }

                        }
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>
