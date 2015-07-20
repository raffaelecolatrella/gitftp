define([
    'text!pages/settings/settings.html'
], function (page) {
    d = Backbone.View.extend({
        el: app.el,
        render: function (id) {
            var that = this;
            this.$el.html(this.el = $('<div class="settings-wrapper bb-loading">'));
            this.template = _.template(page);
            that.el.html(that.template());
        },
    });
    return d;
});