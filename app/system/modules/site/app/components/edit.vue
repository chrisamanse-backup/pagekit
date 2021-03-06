<template>

    <form class="uk-form uk-form-horizontal" name="nodeForm" v-show="node.type" v-on="valid: save">

        <div class="uk-clearfix uk-margin">

            <div class="uk-float-left">

                <h2 class="uk-h2" v-if="node.id">{{ node.title }} ({{ type.label }})</h2>
                <h2 class="uk-h2" v-if="!node.id">{{ 'Add %type%' | trans {type:type.label} }}</h2>

            </div>

            <div class="uk-float-right">

                <a class="uk-button" v-on="click: cancel()">{{ 'Cancel' | trans }}</a>
                <button class="uk-button uk-button-primary" type="submit" v-attr="disabled: form.invalid">{{ 'Save' | trans }}</button>

            </div>

        </div>

        <ul class="uk-tab" v-el="tab">
            <li v-repeat="section: sections | active | orderBy 'priority'"><a>{{ section.label | trans }}</a></li>
        </ul>

        <div class="uk-switcher uk-margin" v-el="content">
            <div v-repeat="section: sections | active | orderBy 'priority'">
                <div v-component="{{ section.name }}" node="{{ node }}" form="{{ nodeForm }}" type="{{ type }}"></div>
            </div>
        </div>

    </form>

</template>

<script>

    var _ = require('lodash');
    var UIkit = require('uikit');

    module.exports = {

        inherit: true,

        ready: function() {
            this.tab = UIkit.tab(this.$$.tab, { connect: this.$$.content });
        },

        watch: {

            selected: function(node) {
                this.$set('node', _.merge({}, node));
                this.tab.switcher.show(0);
            }

        },

        filters: {

            active: function(sections) {

                var type = this.$get('type.id');

                return sections.filter(function(section) {
                    return !section.active || type && type.match(section.active);
                });
            }

        },

        computed: {

            type: function() {
                return _.find(this.types, { id: this.node.type }) || {};
            },

            path: function() {
                return (this.node.path ? this.node.path.split('/').slice(0, -1).join('/') : '') + '/' + (this.node.slug || '');
            },

            sections: function() {
                return this.$root.sections
            }

        },

        events: {

            save: function() {

                var prev = _.find(this.nodes, 'data.frontpage');
                if (this.$get('node.data.frontpage') && prev && prev !== this.node) {
                    delete prev.data.frontpage;
                    this.Nodes.save({ id: prev.id }, {node: prev});
                }

            }

        },

        methods: {

            save: function (e) {

                e.preventDefault();

                var data = { node: this.node }, type = this.type;

                this.$emit('save', data);
                this.$broadcast('save', data);

                this.Nodes.save({ id: this.node.id }, data, function(node) {

                    this.selected.id = parseInt(node.id);
                    this.load();

                    UIkit.notify(this.$trans('%type% saved.', {type: type.label}));

                });
            },

            cancel: function() {
                this.load()
            }

        },

        partials: {

            'settings': require('./settings.html')

        }

    }

</script>
