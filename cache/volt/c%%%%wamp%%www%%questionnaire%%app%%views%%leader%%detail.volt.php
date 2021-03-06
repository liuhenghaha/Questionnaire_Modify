<!--引入时间控件样式表-->
<link rel="stylesheet" href="/datetimepicker/bootstrap-datetimepicker.min.css" />
<!--引入时间控件js-->
<script type="text/javascript" src= '/datetimepicker/bootstrap.min.js'></script>
<script type="text/javascript" src="/datetimepicker/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="/datetimepicker/bootstrap-datetimepicker.zh-CN.js"></script>
<!-- jqgrid 组件-->
<script type="text/javascript" src="/jqGrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="/jqGrid/js/i18n/grid.locale-cn.js"></script>
<!--jqgrid 辅助-->
<script type="text/javascript" src="/jqGrid/js/jqgrid.assist.js"></script>
<!--文件上传 -->
<script src='/fileupload/ajaxfileupload.js'></script>
<!--pm 页面公用函数库-->
<script src='/js/pm.assit.js'></script>

<!-- jqgrid页面引用 -->
<div style="width:100%;height:550px;overflow:hidden;">
    <table id="grid-table"></table>
    <div id="grid-pager"></div>   
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">提示信息</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
$('#myModal').on('hidden.bs.modal', function (e) {
        $('.Leo_question').css('width','860px')
});
$('#myModal').on('hide.bs.modal', function (e) {
        $('.Leo_question').css('width','860px')
});  
$(function(){
    $('#submit').attr('disabled', true);
    getInfo();
    function getInfo(){
        var lastsel;
        var grid_selector = "#grid-table";
        var pager_selector = "#grid-pager";
        //resize to fit page size
        $(window).on('resize.jqGrid', function () {
            $(grid_selector).jqGrid( 'setGridWidth', $(".page-content").width() );
        });
        //resize on sidebar collapse/expand
        var parent_column = $(grid_selector).closest('[class*="col-"]');
        $(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
            if( event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed' ) {
                $(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
            }
        })
        
        jQuery(grid_selector).jqGrid({
            url: "/leader/listexaminee/0",
            datatype: "json",
            height: '311px',
            shrinkToFit:false,
            hidegrid:false,
            autowidth:true,
            colModel:[
                     {   name:'id',  label:'用户id',  index:'id',  width:100, fixed:true, resizable:false, sortable:false, sorttype:"int", align:'center',
                         hidden:true,
                     },
                     {   name:'number',  label:'用户账号',  index:'number',  width:120, fixed:true, resizable:false, sortable:true, sorttype:"int", align:'center',
                         search:true, searchoptions: { sopt: ['eq'], },
                         searchrules:{ required: true, integer:true,},
                     },
                     {   name:'',label:'详情', index:'', width:60, fixed:true, resizable:false,sortable:false, align:'center',
                         search:false,
                         formatter:function(cellvalue, options, rowObject){
                            return "<div class='ui-pg-div ui-inline-edit' data-original-title='查看个人详细信息'><a href='/leader/info/"+rowObject.id+"/0'><i class='fa fa-th-list'></i></a></div>"
                         },
                         viewable:false,
                     },
                     {  name:'name', label:'姓名',  index:'name', width:120, fixed:true, resizable:false, sortable:true, align:'center',
                        search:true, searchoptions: {  sopt: ['eq']  },
                        searchrules:{ required: true, },
                     },
                     {  name:'last_login', label:'最后登录时间', index:'last_login', width:200, fixed:true, resizable:false,  sortable:true, sorttype:'date', align:'center',
                        search:true, searchoptions: {  sopt: ['bw', 'ew'],
                        dataInit:function(element) { 
                            $(element).parent().addClass("input-group date form_date");
                            $(element).addClass('form-control');
                            $(element).datetimepicker({
                                language: 'zh-CN', //汉化 
                                format:'yyyy-mm-dd' , 
                                autoclose:true,
                                minView:2,
                            });    
                            } 
                         },  
                     },
                     {  name:'state', label:'是否测评结束', index:'interview_state', sortable:true,width:90, fixed:true, resizable:false, editable:false, align:'center',
                        search:true,
                        stype:'select', searchoptions:{ sopt: ['eq'], value:"true:是;false:否", },
                        searchrules:{ required: true,},
                        formatter:function(cellvalue){
                            if(cellvalue >= 4 ){
                                return '<span style=\'color:green\'>是</span>';
                            }else{
                                return '<span style=\'color:red\'>否</span>';
                            }
                        },      
                     }, 
                     {  name:'state', label:'导出胜任力报告', index:'state', sortable:false,width:120, fixed:true, resizable:false,align:'center',
                        search:false,
                        viewable:true,
                        formatter:function(cellvalue,options,rowObject){
                            if (rowObject.state >= 5) {
                                return "<div class='ui-pg-div ui-inline-edit' data-original-title='导出胜任力报告'>"+
                                "<span style='visibility:hidden;'>&nbsp;</span>"+
                                "<a href='#'><i class=\"glyphicon glyphicon-download\" onclick='downloadCompetencyReport("+rowObject.id+")'></i></a>"+
                                "<span style='visibility:hidden;'>&nbsp;</span></div>";
                            }else {
                                return '';
                            }  
                        },
                     }, 
                      {  name:'state', label:'导出综合素质报告', index:'state', sortable:false,width:120, fixed:true, resizable:false, align:'center',
                        search:false,
                        viewable:true,
                        formatter:function(cellvalue,options,rowObject){
                            if (rowObject.state >= 5) {
                                return "<div class='ui-pg-div ui-inline-edit' data-original-title='导出综合素质报告'>"+
                                "<span style='visibility:hidden;'>&nbsp;</span>"+
                                "<a href='#'><i class=\"glyphicon glyphicon-download\" onclick='downloadComReport("+rowObject.id+")'></i></a>"+
                                "<span style='visibility:hidden;'>&nbsp;</span></div>"
                            }else {
                                return '';
                            }  
                        },
                     },     
            ], 
            viewrecords : true, 
            rowNum:10,
            rowList:[10,20,50,100,200],
            pager : pager_selector,
            altRows: true,
            emptyrecords: "<span style='color:red'>还未有记录</span>", 
            loadComplete : function(data) {
                if(data.error){
                         $('.Leo_question').css('width','843px')
                         $('.modal-body').html('');
                         $('.modal-body').html(
                         "<p class=\"bg-danger\" style='padding:20px;'>"+data.error+ "</p>"
                         );
                        $('.modal-footer').html('');
                        $('.modal-footer').html(
                         "<a href='/managerlogin'><button type=\"button\" class=\"btn btn-primary\" style='padding:5px 20px;'>重新登录</button></a>"
                        );
                        $('#myModal').modal({
                         keyboard:true,
                         backdrop:'static'
                       })
                }
                var table = this;
                setTimeout(function(){
                    updatePagerIcons(table);
                    enableTooltips(table);
                }, 0);
            },
            reloadAfterSubmit:true,
            caption: "项目管理",
            multiselect: true,
            //multikey: "ctrlKey",
            multiboxonly: true,
        });
        
        //navButtons
        jQuery(grid_selector).jqGrid('navGrid',pager_selector,
            {   //navbar options
                add: false,
                edit: false,
                del: false,
                refresh: true,
                refreshicon : 'ace-icon fa fa-refresh green',
                refreshtext:'刷新',
                search:true,
                searchicon : 'ace-icon fa fa-search orange',
                searchtext:'搜索',
                view: true,
                viewicon : 'ace-icon fa fa-search-plus grey',
                viewtext:'查看',
            },
            {},
            {},
            {},
            {//search
                top : 80,  //位置
                left: 300, //位置 
                multipleSearch: false,
                caption:'搜索查询...',
                Reset: '重置',
                Find:'查询',
                closeAfterSearch:true,
                afterShowSearch: function(e){
                    var form = $(e[0]);
                    form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
                    style_search_form(form);
                },
            },
            {//view
                top : 10,  //位置
                left: 10, //位置 
             }, 
             {//refresh   
             }
        );     
}
});

function downloadCompetencyReport(examinee_id){
    $.post('/file/LgetIndividualCompetencyReport', {'examinee_id':examinee_id}, function(data){
        if (data.error){
            downloadError(data.error);
        }else{
            downloadSuccess(data.success);
        }
    });
}

function downloadComReport(examinee_id){
    $.post('/file/LgetIndividualComReport', {'examinee_id':examinee_id}, function(data){
      if (data.error){
        downloadError(data.error);
      }else{
        downloadSuccess(data.success);
      }
    });
}
function downloadError(msg){
       $('.Leo_question').css('width','843px')
         $('.modal-body').html('');
         $('.modal-body').html(
                         "<p class=\"bg-danger\" style='padding:20px;'>"+msg+ "</p>"
          );
         $('.modal-footer').html('');
         $('.modal-footer').html(
           "<button type=\"button\" class=\"btn btn-primary\" style='padding:5px 20px;'data-dismiss=\"modal\">返回</button>"
             );
         $('#myModal').modal({
            keyboard:true,
            backdrop:'static'
         })
}
function downloadSuccess(msg){
  
       $('.Leo_question').css('width','843px')
         $('.modal-body').html('');
         $('.modal-body').html(
                         "<p class=\"bg-success\" style='padding:20px;'>"+msg+ "</p>"
          );
         $('.modal-footer').html('');
         $('.modal-footer').html(
           "<button type=\"button\" class=\"btn btn-primary\" style='padding:5px 20px;'data-dismiss=\"modal\">关闭</button>"
             );
         $('#myModal').modal({
            keyboard:true,
            backdrop:'static'
         })
}
</script>