<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('larafly-apidoc.app_name','Larafly Apidoc')}}</title>
    <link rel="icon" type="image/png" href="/larafly-apidoc/assets/images/logo.png">
    <!-- Styles -->
    <link href="/larafly-apidoc/assets/css/element.css" rel="stylesheet" type="text/css">
    <link href="/larafly-apidoc/assets/css/index.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="app" class="content" v-cloak>
    @verbatim
    <el-container style="height: 100vh;">
        <!-- left -->
        <el-aside width="260px" style="border-right: 1px solid #eee; padding: 10px;">
            <!-- Logo -->
            <div style="display: flex; align-items: center; justify-content: center;">
                <img src="/larafly-apidoc/assets/images/logo.png" style="width: 40px; margin-right: 10px;" />
                <p style="font-size: 24px; margin: 0px;color: #FF2D20;">{{lang.app_name}}</p>
            </div>
                <!-- 搜索框 -->
                <el-input
                    ref="searchInput"
                    v-model="searchKeyword"
                    :placeholder="lang.search_placeholder"
                    @input="onSearch"
                    clearable
                    prefix-icon="el-icon-search"
                    style="margin: 15px 0;"></el-input>
            <el-tree
                class="filter-tree"
                :data="tree"
                :props="defaultProps"
                default-expand-all
                :filter-node-method="filterNode"
                @node-click="handleTreeClick"
                :render-content="renderContent"
                ref="treeRef">
            </el-tree>
       </el-aside>

       <!-- 中间内容区域 -->
        <el-container>
            <el-main style="padding: 0 10px; display: flex; flex-direction: column; height: 100%;">
                <div v-if="openTabs.length === 0" style="height: 90%; display: flex; align-items: center; justify-content: center;">
                    <div style="display: flex; align-items: center;">
                        <img src="/larafly-apidoc/assets/images/logo.png" style="width: 90px; margin-right: 10px;" />
                        <p class="brand" >{{ lang.app_name }}</p>
                    </div>
                </div>
                <div v-else>
                <el-tabs v-model="activeTab" type="card" closable @tab-remove="handleTabRemove" style="flex-shrink: 0;">
                    <el-tab-pane
                        v-for="tab in openTabs"
                        :key="tab.id"
                        :label="tab.name"
                        :name="tab.id"
                    >
                        <div style="overflow-y: auto; max-height: calc(100vh - 100px); padding: 15px;">
                            <h2>{{ tab.name }}</h2>
                            <p class="desc" v-if="tab.desc">{{ tab.desc }}</p>
                            <h3>
                                <el-tag :type="tab.request_type==='GET'?'success':'danger'">
                                    {{ tab.request_type }}
                                </el-tag>
                                <span class="ml5">{{ tab.url }}</span>
                            </h3>
                            <div class="meta-row">
                                <span class="label">{{ lang.creator }}</span>
                                <span class="value">{{ tab.creator }}</span>
                                <span class="label" v-if="tab.creator!=tab.updater">{{ lang.updater }}</span>
                                <span class="value" v-if="tab.creator!=tab.updater">{{ tab.updater }}</span>
                                <span class="label">{{ lang.update_time }}</span>
                                <span class="value">{{ tab.updated_at }}</span>
                            </div>

                            <h3>{{ lang.request_param }}</h3>

                            <el-table
                                :data="tab.request_data"
                                border
                                row-key="name"
                                default-expand-all
                                :empty-text="lang.no_data"
                                :header-cell-style="{
                                backgroundColor: '#0088CC',
                                color: '#ffffff',
                                fontWeight: 'bold'
                              }"
                                style="width: 100%">
                                <el-table-column :label="lang.name" width="200">
                                    <template slot-scope="scope">
                                        <span  @click="copyText(scope.row.name)" style="color: #3a8ee6;cursor: pointer">
                                            {{ scope.row.name }}
                                        </span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="type"
                                    width="150"
                                    :label="lang.type">
                                    <template slot-scope="scope">
                                        <span :style="{ color: scope.row.type === 'array' || scope.row.type === 'object' ? '#E6A23C' : '#000' }">
                                          {{ scope.row.type }}
                                        </span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="required"
                                    width="100"
                                    :label="lang.is_required">
                                    <template slot-scope="scope">
                                        <span style="color: red" v-if="scope.row.is_required">{{ lang.yes }}</span>
                                        <span v-else>{{ lang.no }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="desc"
                                    :label="lang.desc">
                                </el-table-column>
                            </el-table>
                            <h3>{{ lang.response_param }}</h3>
                            <el-table
                                :data="tab.response_data"
                                border
                                row-key="name"
                                default-expand-all
                                :header-cell-style="{
                                backgroundColor: '#0088CC',
                                color: '#ffffff',
                                fontWeight: 'bold'
                              }"
                                style="width: 100%">
                                <el-table-column :label="lang.name">
                                    <template slot-scope="scope">
                                        <span  @click="copyText(scope.row.name)" style="color: #3a8ee6;cursor: pointer">
                                            {{ scope.row.name }}
                                        </span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="type"
                                    :label="lang.type">
                                    <template slot-scope="scope">
                                        <span :style="{ color: scope.row.type === 'array' || scope.row.type === 'object' ? '#E6A23C' : '#000' }">
                                          {{ scope.row.type }}
                                        </span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="desc"
                                    :label="lang.desc">
                                </el-table-column>
                            </el-table>

                            <h3>{{ lang.response_demo }}</h3>
                            <el-card class="json-card">
                                <pre v-html="highlightJson(tab.response_demo)"></pre>
                            </el-card>
                        </div>
                    </el-tab-pane>
                </el-tabs>
                </div>
            </el-main>
        </el-container>

        <!-- 右侧栏 -->
        <el-aside style="width:70px;border-left: 1px solid #eee; padding: 20px;">
            <div class="project-info">
                <a href="https://github.com/larafly/apidoc" target="_blank" title="larafly-apidoc">
                    <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png"
                         alt="GitHub" style="width: 40px; height: 40px;" />
                </a>
                <a href="https://apidoc.pp-lang.tech" target="_blank" title="document">
                    <i class="el-icon-question" style="font-size: 30px;color: #1a202c"></i>
                </a>

                <el-dropdown style="margin-top: auto;">
          <span class="el-dropdown-link" style="color: #1a202c;font-size: 17px;cursor: pointer">
            <svg t="1750154511079" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="22617" width="30" height="30"><path d="M541.636727 0q99.129741 0 186.506986 37.812375t152.271457 102.706587 102.706587 152.271457 37.812375 186.506986q0 100.151697-37.812375 187.528942t-102.706587 152.271457-152.271457 102.706587-186.506986 37.812375-186.506986-37.812375-152.271457-102.706587-102.706587-152.271457-37.812375-187.528942q0-99.129741 37.812375-186.506986t102.706587-152.271457 152.271457-102.706587 186.506986-37.812375zM672.447106 612.151697q3.065868-4.087824-3.065868-10.219561t-14.307385-13.796407-14.307385-15.840319-4.087824-15.329341q5.10978-16.351297 3.576846-24.015968t-9.708583-10.219561-23.50499-2.043912-36.790419 0.510978q-11.241517 0-18.39521 1.021956t-13.285429 1.532934-13.285429 1.021956-16.351297-0.510978q-14.307385-1.021956-21.972056-5.10978t-14.307385-8.175649-16.862275-7.153693-29.636727-4.087824q-27.592814-1.021956-39.345309-2.55489t-16.862275-3.065868-8.175649-2.043912-12.263473-0.510978q-9.197605 1.021956-28.103792 9.197605t-38.323353 18.906188-33.724551 21.972056-14.307385 18.39521q0 20.439122 2.55489 32.702595t7.153693 18.906188 9.708583 10.730539 10.219561 9.197605 13.796407 7.153693 18.906188 2.55489 18.906188 2.55489 13.796407 7.153693q10.219561 12.263473 21.461078 10.219561t19.928144-6.642715 14.307385-3.576846 6.642715 19.417166q0 8.175649 3.065868 12.263473t8.175649 6.131737l12.263473 6.131737q7.153693 4.087824 15.329341 11.241517 8.175649 8.175649 5.620758 15.329341t-7.664671 13.796407-9.708583 14.307385 0.510978 14.818363q7.153693 11.241517 15.329341 16.351297t15.840319 8.686627 13.796407 8.175649 8.175649 14.818363q0 3.065868 7.153693 11.752495t13.796407 17.884232 10.730539 15.329341-2.043912 5.10978q12.263473 2.043912 26.05988 2.043912t17.884232-5.10978l2.043912-2.043912 0-1.021956 3.065868-1.021956q2.043912-1.021956 5.620758-2.55489t9.708583-6.642715q12.263473-8.175649 23.50499-21.461078t19.417166-29.125749 13.796407-32.191617 7.664671-30.658683q2.043912-13.285429 6.642715-19.417166t8.175649-10.730539 6.131737-12.263473-0.510978-26.05988q-3.065868-23.50499 5.620758-34.235529t14.818363-17.884232zM773.620758 772.598802q14.307385-17.373253 14.818363-28.61477t-24.015968-17.373253q-25.548902-6.131737-36.279441-6.642715t-19.928144 10.730539-13.285429 22.994012 2.043912 32.191617q3.065868 9.197605 5.10978 14.818363t5.10978 7.153693 9.197605 1.021956 17.373253-3.576846q12.263473-3.065868 23.50499-14.307385t16.351297-18.39521zM801.213573 474.187625q7.153693-7.153693 24.015968-10.730539t34.235529-9.197605 30.658683-15.840319 14.307385-28.61477 0-33.724551-6.131737-30.658683-13.796407-32.191617-21.972056-38.323353q-14.307385-22.483034-26.05988-37.812375t-22.994012-26.05988-23.50499-19.417166-27.592814-17.884232q-16.351297-9.197605-32.191617-19.928144t-34.235529-14.307385-39.856287 4.598802-49.053892 35.768463l-17.373253 18.39521q-11.241517 10.219561-23.50499 19.417166t-23.50499 15.840319-20.439122 6.642715q-8.175649 0-25.548902 2.043912t-35.257485 10.219561-30.658683 22.994012-10.730539 39.345309q1.021956 27.592814 9.708583 35.768463t22.483034 9.197605 32.702595 2.043912 40.367265 13.285429q25.548902 6.131737 48.031936 12.263473 19.417166 5.10978 39.345309 12.774451t33.213573 15.840319q15.329341 9.197605 12.263473 24.015968t-10.730539 30.147705-12.774451 29.636727 6.131737 22.483034q15.329341 11.241517 22.483034 14.307385t15.329341 1.532934 22.483034-4.598802 43.944112-3.065868q-8.175649 0-3.065868-6.131737t15.840319-15.329341 22.994012-18.906188 20.439122-15.840319z" p-id="22618"></path></svg>
          </span>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="item in language_options" :command="item.value" :key="item.value">
                            <a :href="item.url">{{ item.label }}</a>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
        </el-aside>
    </el-container>

    @endverbatim
</div>

<!-- Scripts -->
<script src="/larafly-apidoc/assets/js/vue.js"></script>
<script src="/larafly-apidoc/assets/js/axios.js"></script>
<script src="/larafly-apidoc/assets/js/element-2.15.js"></script>
<script>
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    /**
     * get request
     * @param url
     * @param params
     * @returns {Promise<any>}
     */
    Vue.prototype.doGet=function(url,params={}){
        return new Promise((resolve,reject) => {
            axios.get(url,{
                params: params
            }).then((res)=>{
                if(res.status==200){
                    resolve(res.data);
                }
                Vue.$message.error('wrong');
            }).catch(function (error) {
                reject(error);
            });
        });
    }
    /**
     * post request
     * @param url
     * @param params
     * @returns {Promise<any>}
     */
    Vue.prototype.doPost=function(url,params={}){
        return new Promise((resolve,reject) => {
            axios.post(url,params).then((res)=>{
                if(res.status==200){
                    resolve(res.data);
                }
                Vue.$message.error('wrong');
            }).catch(function (error) {
                reject(error);
            });
        });
    }
</script>
<script>
    const vm = new Vue({
        el: '#app',
        data: function () {
            return {
                lang:{
                    app_name:'@lang('larafly-apidoc::apidoc.app_name')',
                    search_placeholder:'@lang('larafly-apidoc::apidoc.search_placeholder')',
                    request_type:'@lang('larafly-apidoc::apidoc.request_type')',
                    creator:'@lang('larafly-apidoc::apidoc.creator')',
                    updater:'@lang('larafly-apidoc::apidoc.updater')',
                    update_time:'@lang('larafly-apidoc::apidoc.update_time')',
                    request_param:'@lang('larafly-apidoc::apidoc.request_param')',
                    response_param:'@lang('larafly-apidoc::apidoc.response_param')',
                    name:'@lang('larafly-apidoc::apidoc.name')',
                    type:'@lang('larafly-apidoc::apidoc.type')',
                    is_required:'@lang('larafly-apidoc::apidoc.is_required')',
                    desc:'@lang('larafly-apidoc::apidoc.desc')',
                    yes:'@lang('larafly-apidoc::apidoc.yes')',
                    no:'@lang('larafly-apidoc::apidoc.no')',
                    no_data:'@lang('larafly-apidoc::apidoc.no_data')',
                    response_demo:'@lang('larafly-apidoc::apidoc.response_demo')',
                },
                searchKeyword: '',
                tree:@json($tree),
                defaultProps: {
                    children: 'children',
                    label: 'name'
                },
                openTabs: [],
                activeTab: '',
                language_options: [
                    {label: 'English', value: 'en',url:'{{ route('larafly-apidoc.index',['locale'=>'en']) }}'},
                    {label: '简体中文', value: 'zh_CN',url:'{{ route('larafly-apidoc.index',['locale'=>'zh_CN']) }}'}
                ],
            }
        },
        methods: {
            copyText(text) {
                let copyContent = document.createElement('input')
                copyContent.value = text
                document.body.appendChild(copyContent)
                copyContent.select()
                document.execCommand('copy')
                document.body.removeChild(copyContent);
                vm.$message.success('@lang('larafly-apidoc::apidoc.copy_success')');
            },
            filterNode(value, data) {
                if (!value) return true;
                return data.name && data.name.toLowerCase().includes(value.toLowerCase());
            },
            onSearch(val) {
                this.$refs.treeRef.filter(val);
            },
            handleTreeClick(item) {
                if(!item.children){
                    const tab = this.openTabs.find(t => t.id === item.id)
                    if (!tab) {
                        this.openTabs.push(item)
                    }
                    this.activeTab = item.id
                }
            },
            handleTabRemove(id) {
                this.openTabs = this.openTabs.filter(t => t.id !== id)
                if (this.activeTab === id && this.openTabs.length > 0) {
                    this.activeTab = this.openTabs[0].id
                }
            },
            renderContent(h, { node, data }) {
                const isParent = data.children && data.children.length > 0;
                const highlight = text => this.highlightText(h, text, this.searchKeyword);
                if(isParent){
                    return h('span', { class: 'custom-tree-node tree-parent' }, [
                        h('svg', { attrs: { viewBox: '0 0 16 16', width: 16, height: 16 }, domProps: {
                                innerHTML: `
        <path opacity="0.01" fill="#C4C4C4" d="M0 0h16v16H0z"></path> <path d="M6.006 1c.367 0 .55 0 .723.041.153.037.3.098.433.18.152.093.282.223.54.482l.595.594c.26.26.39.39.54.482a1.5 1.5 0 0 0 .434.18C9.444 3 9.627 3 9.994 3H13.6c.84 0 1.26 0 1.581.163a1.5 1.5 0 0 1 .655.656c.164.32.164.74.164 1.581v7.2c0 .84 0 1.26-.164 1.581a1.5 1.5 0 0 1-.655.655c-.32.164-.74.164-1.581.164H2.4c-.84 0-1.26 0-1.581-.164a1.5 1.5 0 0 1-.656-.655C0 13.861 0 13.441 0 12.6V3.4c0-.84 0-1.26.163-1.581a1.5 1.5 0 0 1 .656-.656C1.139 1 1.559 1 2.4 1h3.606Z" fill="#0980FF"></path>
      `
                            }, style: { marginRight: '6px' } }),
                        highlight(data.name)
                    ]);
                }
                const request_type = data.request_type;
                const type = request_type==='GET'?'success':'danger'
                return h('span', { class: 'custom-tree-node' }, [
                    h('i', { class: 'el-icon-document', style: { marginRight: '6px' } }),
                    highlight(data.name),
                    h('el-tag', {attrs: { size: "mini",type: type},style: { marginLeft: '6px' }},request_type),
                ]);
            },
            highlightText(h, text, keyword) {
                if (!keyword) return h('span', text);
                const index = text.toLowerCase().indexOf(keyword.toLowerCase());
                if (index === -1) return h('span', text);

                const before = text.slice(0, index);
                const match = text.slice(index, index + keyword.length);
                const after = text.slice(index + keyword.length);

                return h('span', [
                    before,
                    h('span', { style: { color: '#f56c6c', fontWeight: 'bold' } }, match),
                    after,
                ]);
            },
            handleGlobalKey(e) {
                if (e.key === '/' && !e.metaKey && !e.ctrlKey) {
                    const inputEl = this.$refs.searchInput?.$el?.querySelector('input');
                    if (!inputEl) return;

                    // 已经聚焦就不再聚焦
                    if (document.activeElement === inputEl) return;

                    e.preventDefault();
                    this.$nextTick(() => {
                        inputEl.focus();
                    });
                }
            },
            highlightJson(json) {
                if (!json) return ''
                if (typeof json !== 'string') {
                    json = JSON.stringify(json, null, 2)
                }
                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                return json.replace(/("(\\u[\da-fA-F]{4}|\\[^u]|[^\\"])*"(?:\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    let cls = 'number'
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'key'
                        } else {
                            cls = 'string'
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'boolean'
                    } else if (/null/.test(match)) {
                        cls = 'null'
                    }
                    return '<span class="json-' + cls + '">' + match + '</span>'
                })
            }
        },
        filters: {
            formatJson(value) {
                if (!value) return ''
                return JSON.stringify(value, null, 2)
            }
        },
        mounted() {
            window.addEventListener('keydown', this.handleGlobalKey);
        },
        beforeDestroy() {
            window.removeEventListener('keydown', this.handleGlobalKey);
        },
    });
</script>
</body>
</html>
