<template>
    <div class="tab-pane padding-20 sm-no-padding slide-left active" id="manage-topics">
        <CreateTopics :show-slider="showTopicCreateSlider"
                      :class-group-syllabus-id="classGroupSyllabusId"
                      :subject-id="subjectId"
                      :chapter-id="chapterId"
                      :subscription-month-id="selectedMonth"
                      :selected-topic="selectedTopic"
                      @on-close="onSliderClose"/>
        <div class="card-heading">
            Manage Topics
            <p class="text-transform-none fs-11">
                You can select a subscription month and then you can create topics against the selected subscription
                month. You can also arrange the topic order by just dragging the topics
            </p>
        </div>
        <div class="row row-same-height">
            <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
                <h6 class="font-weight-light fs-13 mt-2">Assigned Months</h6>
                <div class="card card-default card-bordered mb-1 draggable-card"
                     @click.prevent="getTopics(month.subscription_month_id)"
                     :class="{'active':month.subscription_month_id===selectedMonth}"
                     v-for="month in subscriptionMonths"
                     :key="month.subscription_month_id">
                    <div class="card-header" role="tab">
                        <div class="card-title">
                            <a href="#">
                                {{month.subscription_month_name}}
                            </a>
                        </div>
                        <a href="" class="pull-right pt-0 pb-0 mt--2rem text-success"
                           @click.prevent="getTopics(month.subscription_month_id)">
                            <i class="fa fa-chevron-right fs-12"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-9" id="created-topics">
                <h6 class="font-weight-light fs-13 mt-2">Created Topics</h6>
                <draggable v-model="topics" group="topics" class="row row-same-height"
                           @start="drag=true" @end="drag=false" @change="updateOrder">
                    <div class="col-md-6 col-sm-12" v-for="(topic,index) in topics">
                        <TopicCard :topic="topic"
                                   :key="topic.topic_id"
                                   :index="index+1"
                                   :subscription-month-id="selectedMonth"
                                   :chapter-id="chapterId"
                                   :class-group-syllabus-id="classGroupSyllabusId"
                                   :subject-id="subjectId"
                                   @on-edit-topic="onEdit"
                                   @on-delete="onDelete"/>
                    </div>
                </draggable>
                <div class="card card-borderless mt-5">
                    <div class="card-body text-center align-items-center">
                        <h4 class="font-weight-lighter hint-text mt-5" v-if="selectedMonth&&!topics.length">
                            No topics created against this month. You can create new one by clicking on the below button
                        </h4>
                        <h4 class="font-weight-lighter hint-text mt-5" v-else-if="!selectedMonth">
                            Select one month from the given list to create or view topics
                        </h4>
                    </div>
                </div>
                <div class="row" v-if="selectedMonth">
                    <div class="col-12 text-center">
                        <a href="#" class="btn btn-outline-success" @click.prevent="createNewTopic">
                            <i class="fa fa-plus mr-1"></i> Create New Topic
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import _ from "lodash";
    import draggable from "vuedraggable";
    import SubscriptionMonthsAPI from "../subscriptionMonths/SubscriptionMonthsAPI";
    import TopicsAPI from "./TopicsAPI";
    import TopicCard from "./TopicCard";
    import CreateTopics from "./CreateTopics";

    export default {
        name: "ManageTopics",
        components: {
            CreateTopics,
            TopicCard,
            draggable
        },
        data() {
            return {
                subscriptionMonths: [],
                classGroupSyllabusId: "",
                subjectId: "",
                chapterId: "",
                topics: [],
                selectedMonth: "",
                showTopicCreateSlider: false,
                selectedTopic: null
            }
        },
        mounted() {
            this.fetchDataFromRoutes();
        },
        methods: {
            onSliderClose(status = false) {
                this.showTopicCreateSlider = false;
                this.selectedTopic = null;
                if (status) {
                    this.getTopics(this.selectedMonth, true);
                }
            },
            onEdit(topic) {
                this.selectedTopic = topic;
                this.showTopicCreateSlider = true;
            },
            createNewTopic() {
                this.showTopicCreateSlider = true;
            },
            fetchDataFromRoutes() {
                const route = this.$route;
                this.classGroupSyllabusId = route.params.classGroupSyllabusId;
                this.subjectId = route.params.subjectId;
                this.chapterId = route.params.chapterId;
                if (this.classGroupSyllabusId)
                    this.getAllSubscriptionMonths();
            },
            onDelete() {
                this.getTopics(this.selectedMonth, true);
            },
            getTopics(subscriptionMonthId, isFromCreate = false) {
                if (this.selectedMonth !== subscriptionMonthId)
                    this.selectedMonth = subscriptionMonthId;
                else if (!isFromCreate)
                    return;
                $('#created-topics').block();
                this.$axios.get(TopicsAPI.GET_ALL_TOPICS, {
                    params: {
                        chapter_id: this.chapterId,
                        subscription_month_id: subscriptionMonthId,
                        subject_id: this.subjectId,
                        class_group_syllabus_id: this.classGroupSyllabusId
                    }
                })
                    .then(response => {
                        $('#created-topics').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.topics = responseData.data;
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#created-topics').unblock();
                        $.showNotification("Error occurred while fetching topics. Try again later", "error");
                    })
            },

            getAllSubscriptionMonths() {
                $('#manage-topics').block();
                this.$axios.get(SubscriptionMonthsAPI.GET_ALL_ASSIGNED_SUBSCRIPTION_MONTHS, {
                    params: {
                        class_group_syllabus_id: this.classGroupSyllabusId
                    }
                })
                    .then(response => {
                        $('#manage-topics').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            if (responseData.data)
                                this.subscriptionMonths = responseData.data.subscription_months;
                            else
                                $.showNotification("Invalid request sent! We couldn't find any class groups");
                        } else
                            $.showNotification(responseData.error, "error");
                    })
                    .catch(() => {
                        $('#manage-topics').unblock();
                        $.showNotification("Error occurred while fetching the subscription months", "error");
                    })
            },
            updateOrder() {
                $('#created-topics').block();
                const orderedTopics = _.map(this.topics, "ss_month_topic_id");
                this.$axios.post(TopicsAPI.UPDATE_ORDER, {topics: orderedTopics})
                    .then(response => {
                        $('#created-topics').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            $.showNotification("Topics orders changed successfully", "success");
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $('#created-topics').unblock();
                        $.showNotification("Error occurred while saving the order. Try again later", "error");
                    })
            }
        }
    }
</script>

<style scoped>
    .draggable-card.active {
        background-color: #f8f9fa !important;
    }
</style>
