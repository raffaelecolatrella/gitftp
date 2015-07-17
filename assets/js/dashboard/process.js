define([], function () {

    var process = {
        process_id: 0,
        deployView: function (e) {
            /*
             * Project view page.
             */
            var that = this;
            setTimeout(function () {
                if ($('.is-deploy-view-id').length) {
                    var id = $('.is-deploy-view-id').attr('data-id');
                    $.getJSON(base + 'api/deploy/get/' + id, function (data) {
                        data = data.data[0];
                        var el = $('.project-v-status');
                        if (data.status == 'Idle' || data.status == 'To be initialized') {
                            el.removeClass("project-co-loading");
                            el.html(data.status);
                        } else {
                            el.addClass("project-co-loading");
                            el.html('<i class="fa fa-spin fa-refresh fa-fw"></i> ' + data.status);
                        }
                    });
                }
                if ($('.is-deploy-list').length) {

                    _ajax({
                        url: base + 'api/deploy/getonly/',
                        data: {
                            select: 'id'
                        },
                        method: 'post',
                        dataType: 'json'
                    }).done(function (data) {
                        $.each(data.data, function (i, a) {
                            $target = $('.is-deploy-list[data-id="' + a.id + '"]');
                            $target.find('.status').html('(' + a.status + ')');
                        });
                    });
                }
                that.deployView();
            }, this.deployViewInterval);
        },
        deployViewInterval: 1000,
        timeUpdateInterval: 500,
        timeUpdate: function () {
            /*
             * Time update . class name: .dynamicTime
             */
            var that = this;
            setTimeout(function () {
                if ($('.dynamicTime').length) {

                    $('.dynamicTime').each(function (i, a) {
                        var $this = $(this);
                        var timestamp = (new Date(parseInt($this.attr('data-timestamp')) * 1000)).getTime();
                        var currtime = parseInt((new Date()).getTime().toString());
                        var diff = currtime - timestamp;
                        $this.html(moment.duration(diff).humanize() + ' ago');
                    });

                }
                that.timeUpdate();
                if (this.timeUpdateInterval == 1) {
                    this.timeUpdateInterval = 10000;
                }
            }, this.timeUpdateInterval);

        },
        stickyProjectSidebar: function () {
            $(window).scroll(function () {
                console.log('scroll');
                if ($('.projectview-siderbar').length) {
                    $('.projectview-siderbar').css({
                        'margin-top': $(window).scrollTop()
                    });
                }
            });
        },
        runProcess: function () {
            this.deployView();
            this.timeUpdate();
            //this.stickyProjectSidebar();
        },
        init: function () {
            this.runProcess();
        }
    };


    return process;
});
        