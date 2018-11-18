<template>
  <div class="page">
    <h1 class="title">编辑{{title}}</h1>
    <TitleWithContent :titleError="errors.title" :contentError="errors.content" :title.sync="formData.title" :content.sync="formData.content"></TitleWithContent>
    <FormButtonGroup />
  </div>
</template>

<script>
import FormButtonGroup from '../../components/FormButtonGroup.vue';
import TitleWithContent from '../../components/TitleWithContent.vue';
import Diff from '../../utils/Diff';
let mainDiff = new Diff();
export default {
  components: { TitleWithContent, FormButtonGroup },
  data () {
    return {
      title: '单页',
      errors: {},
      formData: {
        title: '',
        content: ''
      }
    };
  },
  methods: {
    confirm () {
      this.$http.post(`categories/${this.$route.params.id}/page`, mainDiff.diff(this.formData)).then(res => {
        this.$emit('on-loaded');
        this.$emit('on-success');
        this.$Message.success(`编辑单页成功`);
        this.$router.push({name: 'columnList'});
      }).catch(err => {
        this.$emit('on-loaded');
        this.errors = err.response.data.errors;
      });
    }
  },
  mounted () {
    this.$http.get(`categories/${this.$route.params.id}/page`, {
      params: {
        include: 'post_content'
      }
    }).then(res => {
      if (res.data.data) {
        this.formData.title = res.data.data.title;
        this.formData.content = res.data.data.post_content.data.content;
      }
      if (res.data.meta) {
        this.title = res.data.meta.cate_name;
      }
      mainDiff.save(this.formData);
    });
    for (let key in this.formData) {
      this.$watch(`formData.${key}`, () => {
        if (this.errors[key]) {
          delete this.errors[key];
        }
      });
    };
  }
};
</script>

<style lang="less" scoped>
.page{
  .title {
    font-weight: 400;
    line-height: 32px;
    font-size: 20px;
    margin-bottom: 25px;
  }
  .submit_btn_group{
    margin-left: 0;
  }
}
</style>
