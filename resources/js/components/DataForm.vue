<template>
  <el-form
    :model="model"
    :rules="rules"
    :label-position="labelPosition"
    :label-width="labelWidth"
    :disabled="disabled"
    :inline="inline"
    ref="form"
    @submit.native.prevent="handleSubmit"
  >
    <slot></slot>

    <div v-if="showActions" class="data-form-actions">
      <el-button
        v-if="showCancel"
        type="default"
        :size="buttonSize"
        @click="handleCancel"
      >
        {{ cancelText }}
      </el-button>
      <el-button
        type="primary"
        :size="buttonSize"
        :loading="loading"
        native-type="submit"
      >
        {{ submitText }}
      </el-button>
    </div>
  </el-form>
</template>

<script>
export default {
  name: 'DataForm',
  props: {
    model: {
      type: Object,
      required: true
    },
    rules: {
      type: Object,
      default: () => ({})
    },
    labelPosition: {
      type: String,
      default: 'top'
    },
    labelWidth: {
      type: String,
      default: '120px'
    },
    disabled: {
      type: Boolean,
      default: false
    },
    inline: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    showActions: {
      type: Boolean,
      default: true
    },
    showCancel: {
      type: Boolean,
      default: true
    },
    submitText: {
      type: String,
      default: 'Guardar'
    },
    cancelText: {
      type: String,
      default: 'Cancelar'
    },
    buttonSize: {
      type: String,
      default: 'medium'
    }
  },
  methods: {
    validate() {
      return new Promise((resolve, reject) => {
        this.$refs.form.validate((valid) => {
          if (valid) {
            resolve(valid);
          } else {
            reject(new Error('Validation failed'));
          }
        });
      });
    },
    resetFields() {
      this.$refs.form.resetFields();
    },
    clearValidate(props) {
      this.$refs.form.clearValidate(props);
    },
    handleSubmit() {
      this.validate()
        .then(() => {
          this.$emit('submit', this.model);
        })
        .catch(() => {
          this.$emit('validation-error');
        });
    },
    handleCancel() {
      this.$emit('cancel');
    }
  }
};
</script>

<style scoped>
.data-form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
  padding-top: 15px;
  border-top: 1px solid #ebeef5;
}
</style>
