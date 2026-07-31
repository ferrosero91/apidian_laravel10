<template>
  <div class="data-table-wrapper">
    <el-table
      :data="data"
      :stripe="stripe"
      :border="border"
      :fit="fit"
      :show-header="showHeader"
      :highlight-current-row="highlightCurrentRow"
      :empty-text="emptyText"
      :style="{ width: '100%' }"
      v-loading="loading"
      @selection-change="handleSelectionChange"
      @sort-change="handleSortChange"
      @row-click="handleRowClick"
    >
      <!-- Selection column -->
      <el-table-column
        v-if="selection"
        type="selection"
        width="55"
        align="center"
      ></el-table-column>

      <!-- Index column -->
      <el-table-column
        v-if="showIndex"
        type="index"
        width="60"
        label="#"
        align="center"
      ></el-table-column>

      <!-- Dynamic columns -->
      <el-table-column
        v-for="column in columns"
        :key="column.prop || column.label"
        :prop="column.prop"
        :label="column.label"
        :width="column.width"
        :min-width="column.minWidth"
        :fixed="column.fixed"
        :sortable="column.sortable"
        :align="column.align || 'left'"
        :show-overflow-tooltip="column.showOverflowTooltip !== false"
      >
        <template slot-scope="scope" v-if="column.slot">
          <slot :name="column.slot" :row="scope.row" :column="column" :$index="scope.$index"></slot>
        </template>
        <template slot-scope="scope" v-else-if="column.render">
          <span v-html="column.render(scope.row, column, scope.$index)"></span>
        </template>
      </el-table-column>

      <!-- Actions column -->
      <el-table-column
        v-if="$slots.actions"
        label="Acciones"
        :width="actionsWidth"
        fixed="right"
        align="center"
      >
        <template slot-scope="scope">
          <slot name="actions" :row="scope.row" :$index="scope.$index"></slot>
        </template>
      </el-table-column>
    </el-table>

    <!-- Pagination -->
    <div v-if="pagination" class="data-table-pagination">
      <el-pagination
        background
        layout="total, sizes, prev, pager, next, jumper"
        :total="total"
        :page-size="pageSize"
        :current-page="currentPage"
        :page-sizes="pageSizes"
        @current-change="handlePageChange"
        @size-change="handleSizeChange"
      ></el-pagination>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DataTable',
  props: {
    data: {
      type: Array,
      required: true,
      default: () => []
    },
    columns: {
      type: Array,
      required: true,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    },
    stripe: {
      type: Boolean,
      default: true
    },
    border: {
      type: Boolean,
      default: false
    },
    fit: {
      type: Boolean,
      default: true
    },
    showHeader: {
      type: Boolean,
      default: true
    },
    highlightCurrentRow: {
      type: Boolean,
      default: false
    },
    emptyText: {
      type: String,
      default: 'No hay datos'
    },
    selection: {
      type: Boolean,
      default: false
    },
    showIndex: {
      type: Boolean,
      default: false
    },
    actionsWidth: {
      type: [String, Number],
      default: 200
    },
    pagination: {
      type: Boolean,
      default: false
    },
    total: {
      type: Number,
      default: 0
    },
    pageSize: {
      type: Number,
      default: 20
    },
    currentPage: {
      type: Number,
      default: 1
    },
    pageSizes: {
      type: Array,
      default: () => [10, 20, 50, 100]
    }
  },
  methods: {
    handleSelectionChange(selection) {
      this.$emit('selection-change', selection);
    },
    handleSortChange({ column, prop, order }) {
      this.$emit('sort-change', { column, prop, order });
    },
    handleRowClick(row, column, event) {
      this.$emit('row-click', row, column, event);
    },
    handlePageChange(page) {
      this.$emit('page-change', page);
    },
    handleSizeChange(size) {
      this.$emit('size-change', size);
    }
  }
};
</script>

<style scoped>
.data-table-wrapper {
  width: 100%;
}

.data-table-pagination {
  display: flex;
  justify-content: center;
  margin-top: 20px;
  padding: 10px 0;
}

.el-pagination {
  font-size: 13px;
}
</style>
