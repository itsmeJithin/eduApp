<template>
    <quill-editor
        :id="editorId"
        :key="editorId"
        :ref="editorId"
        class="quill-text-editor"
        :options="editorOption"
        v-model="editorData"
        @blur="onBlur()"
        @change="onChange()"/>
</template>

<script>
    import Vue from "vue"
    import VueQuillEditor, {Quill} from 'vue-quill-editor'
    import 'quill/dist/quill.snow.css'

    Vue.use(VueQuillEditor);

    import QuillBetterTable from 'quill-better-table'

    Quill.register({
        'modules/better-table': QuillBetterTable
    }, true);

    import "@wiris/mathtype-generic/wirisplugin-generic.js"

    export default {
        name: "quill-text-editor",
        props: {
            options: {
                type: Object,
            },
            id: {
                type: String,
                default: Math.random().toString(36).substring(2)
            },
            data: {
                type: String,
                default: null
            },
            settings: {
                type: Object,
                default() {
                    return {};
                }
            },
            clear: false,
        },
        model: {
            prop: 'data',
            event: 'change'
        },
        created() {

        },
        data() {
            return {
                editorData: "",
                editorOption: {
                    modules: {
                        toolbar: [
                            [{"header": [1, 2, 3, 4, 5, false]}],
                            [{"font": []}],
                            ["bold", "italic", "underline", "strike"],
                            ["blockquote", "code-block", "link"],
                            [{"list": "ordered"}, {"list": "bullet"}],
                            [{"script": "sub"}, {"script": "super"}],
                            [{"color": []}, {"background": []}],
                            [{"align": []}],
                            [{"indent": "+1"}, {"indent": "-1"}],
                            ["clean"]
                        ]
                    },
                    placeholder: ""
                },
                editorSettings: {
                    cut: true,
                    copy: true,
                    paste: true
                },
                editorId: null
            };
        },
        watch: {
            data() {
                let self = this;
                self.editorData = _.clone(self.data);
            },
            clear() {

                this.editorData = this.clear == true ? "" : "";
            }
        },
        async mounted() {
            const self = this;
            Object.assign(self.editorOption, self.options);
            Object.assign(self.editorSettings, self.settings);
            self.editorId = self.id;
            self.editorData = _.clone(self.data);

            // _.defer(function () {
            //     // Integration of mathType into QuillJS text editor
            //     $('#' + self.editorId + ' .ql-editor').attr("id", self.editorId + '-editor');
            //     $('#' + self.editorId + ' .ql-toolbar').attr("id", self.editorId + '-toolbar');
            //
            //     let genericIntegrationProperties = {};
            //     genericIntegrationProperties.target = document.getElementById(self.editorId + '-editor');
            //     genericIntegrationProperties.toolbar = document.getElementById(self.editorId + '-toolbar');
            //
            //     // GenericIntegration instance.
            //     let genericIntegrationInstance = new WirisPlugin.GenericIntegration(genericIntegrationProperties);
            //     genericIntegrationInstance.init();
            //     genericIntegrationInstance.listeners.fire('onTargetReady', {});
            //     // self.insertTable();
            // });
        },
        updated() {
            const self = this;
            if (self.editorSettings.cut === false) {
                $("#" + self.editorId).bind("cut", function (e) {
                    e.preventDefault();
                });
            }
            if (self.editorSettings.copy === false) {
                $("#" + self.editorId).bind("copy", function (e) {
                    e.preventDefault();
                });
            }
            if (self.editorSettings.paste === false) {
                $("#" + self.editorId).bind("paste", function (e) {
                    e.preventDefault();
                });
            }
        },
        methods: {
            insertTable() {
                const self = this;
                const tableModule = self.editor.getModule('better-table');
                tableModule.insertTable(3, 3);
            },
            onBlur() {
                this.$emit("blur");
            },
            onChange() {
                let self = this;
                self.$emit('change', self.editorData);
            }
        },
        computed: {
            editor() {
                const self = this;
                return (this.$refs[self.editorId]).quill;
            }
        }
    }
</script>

<style>
    .vue-simple-drawer {
        background-color: #fff;
        color: #333;
        z-index: 1050 !important;
    }

    .vue-simple-drawer .close-btn div {
        width: 20px !important;
    }

    .mask {
        z-index: 1049 !important;
    }

    .ql-toolbar img {
        margin-right: 10px;
    }
</style>
