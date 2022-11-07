<template>
    <div :id="'card-linear-'+topic.topic_id" class="card card-default card-bordered draggable-card">
        <div class="card-header  ">
            <div class="card-title">{{topic.topic_name}}
            </div>
            <div class="card-controls">
                #{{index}}
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <p class="hint-text">
                        {{topic.topic_description}}
                    </p>
                </div>
            </div>
        </div>
        <div class="card-footer border-0 bg-white p-0">
            <div class="btn-group btn-block">
                <button class="btn btn-outline-danger" @click.prevent="deleteTopic">
                    <i class="fa fa-trash-o mr-1"></i> Delete
                </button>
                <button class="btn btn-outline-primary" @click="editTopic">
                    <i class="fa fa-pencil mr-1"></i> Edit
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import TopicsAPI from "./TopicsAPI";

    export default {
        name: "TopicCard",
        props: {
            topic: {
                require: true,
                type: Object
            },
            index: {
                required: true,
                type: Number
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
            },
        },
        methods: {
            editTopic() {
                this.$emit("on-edit-topic", this.topic)
            },
            deleteTopic() {
                const self = this;
                $.simpleDialog({
                    title: "Do you want to delete?",
                    message: "This topic and it's study materials will be permanently deleted. Do you want to continue?",
                    confirmBtnText: "Yes! Delete",
                    closeBtnText: "No! Cancel",
                    confirmBtnClass: "btn-danger",
                    closeBtnClass: "btn-success",
                    onSuccess: function () {
                        self.delete();
                    }
                });
            },
            delete() {
                this.$axios.delete(TopicsAPI.DELETE_TOPIC, {
                    params: {
                        topic_id: this.topic.topic_id,
                        class_group_syllabus_id: this.classGroupSyllabusId,
                        chapter_id: this.chapterId,
                        subscription_month_id: this.subscriptionMonthId
                    }
                })
                    .then(response => {
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification("Topics deleted successfully", "success");
                            this.$emit("on-delete", true);
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $.showNotification("Error occurred while deleting topic. Try again later", "error");
                    })
            }
        }
    }
</script>

<style scoped>

</style>
