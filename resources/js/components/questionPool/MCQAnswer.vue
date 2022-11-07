<template>
    <div>
        <label class="text-bold" for="answer-option-1">Option {{ index + 1 }}</label>
        <QuillTextEditor v-model="option.answer"
                         :options="quillOptions"
                         :id="'answer-option-'+option.id"/>
        <div class="form-check form-check-inline switch mt-3">
            <input type="checkbox" :id="'right-option-'+option.id" v-model="option.isRightAnswer">
            <label :for="'right-option-'+option.id">Right Answer</label>
        </div>
        <button class="btn btn-sm btn-danger pull-right mt-3" @click.prevent="removeOption">
            Remove
        </button>
        <hr class="mt-3 mb-3">
    </div>
</template>

<script>
import QuillTextEditor from "../common/QuillTextEditor";

export default {
    name: "MCQAnswer",
    components: {QuillTextEditor},
    props: {
        option: {
            required: true,
        },
        index: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            quillOptions: {
                modules: {
                    // 'better-table': true,
                    toolbar: [
                        [{"script": "sub"}, {"script": "super"}],
                    ],
                    clipboard: {
                        allowed: {
                            tags: ['a', 'b', 'strong', 'u', 's', 'i', 'p', 'br', 'ul', 'ol', 'li', 'span'],
                            attributes: ['href', 'rel', 'target', 'class']
                        },
                        keepSelection: false,
                    }
                },
                placeholder: ""
            },
            editorSettings: {
                cut: true,
                copy: true,
                paste: true
            },
            contentDelta: "",
            content: ""
        }
    },
    methods: {
        removeOption() {
            this.$emit("on-remove-option", this.option);
        }
    }
}
</script>

<style scoped>

</style>
