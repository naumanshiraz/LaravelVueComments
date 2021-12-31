<template>
    <div class="comments-app">
        <h1>Comments</h1>
        <!-- From -->
        <div class="comment-form">
            <form class="form" name="form">
                <div class="form-row">
                    <textarea class="input" placeholder="Add comment..." required v-model="message"></textarea>
                    <span v-if="errorComment" style="color:red">{{errorComment}}</span>
                </div>

                <div class="form-row">
                    <input class="input" placeholder="User Name" type="text" v-model="username">
                    <span v-if="errorUser" style="color:red">{{errorUser}}</span>
                </div>

                <div class="form-row">
                    <input type="button" class="btn btn-primary btn-sm" @click="saveComment" value="Add Comment">
                </div>
            </form>
        </div>
        <!-- Comments List -->
        <div class="comments" v-if="comments" v-for="(comment,index) in commentsData">
            <!-- Comment -->
            <div class="comment" v-if="comment.replies">
                <!-- Comment Box -->
                <div class="comment-box">
                    <div class="comment-header row">
                        <div class="col-xs-10">
                            <span class="comment-author">
                                {{ comment.name}}
                            </span>
                            <span class="comment-date">
                                <vue-moments-ago prefix="posted" suffix="ago" :date="comment.date" lang="en"/>
                            </span>
                        </div>
                        <div class="col-xs-2 text-right">
                            <a v-on:click="openComment(index)"
                               class="btn btn-warning btn-xs text-white">REPLY</a>
                        </div>
                    </div>

                    <div class="comment-text">{{comment.comment}}</div>
                </div>
                <!-- From -->
                <div class="comment-form comment-v" v-if="commentBoxs[index]">
                    <form class="form" name="form">
                        <div class="form-row">
                            <textarea class="input" placeholder="Add comment..." required v-model="message"></textarea>
                            <span v-if="errorReply" style="color:red">{{errorReply}}</span>
                        </div>

                        <div class="form-row">
                            <input class="input" placeholder="User Name" type="text" v-model="username">
                            <span v-if="errorUser" style="color:red">{{errorUser}}</span>
                        </div>

                        <div class="form-row">
                            <input type="button" class="btn btn-success"
                                   v-on:click="replyComment(comment.commentid,index)" value="Add Comment">
                        </div>
                    </form>
                </div>
                <!-- Comment - Reply -->
                <div v-if="comment.replies">
                    <div class="comments" v-for="(replies,index2) in comment.replies">
                        <div class="comment reply">
                            <!-- Comment Box -->
                            <div class="comment-box">
                                <div class="comment-header row">
                                    <div class="col-xs-10">
                                        <span class="comment-author">
                                            {{replies.name}}
                                        </span>
                                        <span class="comment-date">
                                            <vue-moments-ago prefix="posted" suffix="ago" :date="replies.date" lang="en"/>
                                        </span>
                                    </div>
                                    <div class="col-xs-2 text-right">
                                        <a v-on:click="replyCommentBox(index)"
                                           class="btn btn-warning btn-xs text-white">REPLY</a>
                                    </div>
                                </div>
                                <div class="comment-text">{{replies.comment}}</div>
                            </div>
                            <!-- From -->
                            <div class="comment-form reply" v-if="replyCommentBoxs[index2]">
                                <form class="form" name="form">
                                    <div class="form-row">
                                        <textarea class="input" placeholder="Add comment..." required
                                                  v-model="message"></textarea>
                                        <span v-if="errorReply" style="color:red">{{errorReply}}</span>
                                    </div>

                                    <div class="form-row">
                                        <input class="input" placeholder="User Name" type="text" v-model="username">
                                        <span v-if="errorUser" style="color:red">{{errorUser}}</span>
                                    </div>

                                    <div class="form-row">
                                        <input type="button" class="btn btn-success"
                                               v-on:click="replyComment(comment.commentid,index)" value="Add Comment">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import VueMomentsAgo from 'vue-moments-ago'

    var _ = require('lodash');
    export default {
        props: ['commentUrl'],
        components: {
            VueMomentsAgo
        },
        data() {
            return {
                comments: [],
                commentreplies: [],
                commentBoxs: [],
                message: null,
                username: null,
                replyCommentBoxs: [],
                commentsData: [],
                viewcomment: [],
                show: [],
                spamCommentsReply: [],
                spamComments: [],
                errorComment: null,
                errorUser: null,
                errorReply: null,
            }
        },
        http: {
            headers: {
                'X-CSRF-TOKEN': window.csrf
            }
        },
        methods: {
            fetchComments() {
                this.$http.get('comments/' + this.commentUrl).then(res => {
                    this.commentData = res.data;
                    this.commentsData = _.orderBy(res.data, ['date'], ['desc']);
                    this.comments = 1;
                });
            },
            showComments(index) {
                if (!this.viewcomment[index]) {
                    Vue.set(this.show, index, "hide");
                    Vue.set(this.viewcomment, index, 1);
                } else {
                    Vue.set(this.show, index, "view");
                    Vue.set(this.viewcomment, index, 0);
                }
            },
            openComment(index) {
                if (this.commentBoxs[index]) {
                    Vue.set(this.commentBoxs, index, 0);
                } else {
                    Vue.set(this.commentBoxs, index, 1);
                }
            },
            replyCommentBox(index) {
                if (this.replyCommentBoxs[index]) {
                    Vue.set(this.replyCommentBoxs, index, 0);
                } else {
                    Vue.set(this.replyCommentBoxs, index, 1);
                }
            },
            saveComment() {
                if (this.message == null || this.message == ' ') {
                    this.errorComment = "Please enter a comment to save";
                }

                if (this.username == null || this.username == ' ') {
                    this.errorUser = "Please enter your username";
                }

                if (this.message && this.username) {
                    this.errorComment = null;
                    this.errorUser = null;
                    this.$http.post('comments', {
                        comment: this.message,
                        username: this.username
                    }).then(res => {
                        if (res.data.status) {
                            this.commentsData.push({
                                "commentid": res.data.commentId,
                                "name": this.username,
                                "comment": this.message,
                                "reply": 0,
                                "replies": [],
                                "date": "Just Now"
                            });
                            this.message = null;
                            this.username = null;
                        }
                    });
                }
            },
            replyComment(commentId, index) {
                if (this.message == null || this.message == ' ') {
                    this.errorReply = "Please enter a comment to save";
                }

                if (this.username == null || this.username == ' ') {
                    this.errorUser = "Please enter your username";
                }

                if (this.message && this.username) {
                    this.errorReply = null;
                    this.$http.post('comments', {
                        comment: this.message,
                        username: this.username,
                        reply_id: commentId
                    }).then(res => {

                        if (res.data.status) {
                            if (!this.commentsData[index].reply) {
                                this.commentsData[index].replies.push({
                                    "commentid": res.data.commentId,
                                    "name": this.username,
                                    "comment": this.message,
                                    "date": "Just Now"
                                });
                                this.commentsData[index].reply = 1;
                                Vue.set(this.replyCommentBoxs, index, 0);
                                Vue.set(this.commentBoxs, index, 0);
                            } else {
                                this.commentsData[index].replies.push({
                                    "commentid": res.data.commentId,
                                    "name": this.username,
                                    "comment": this.message,
                                    "date": "Just Now"
                                });
                                Vue.set(this.replyCommentBoxs, index, 0);
                                Vue.set(this.commentBoxs, index, 0);
                            }
                            this.message = null;
                            this.username = null;
                        }
                    });
                }
            }
        },
        mounted() {
            console.log("mounted");
            this.fetchComments();
        }

    }
</script>