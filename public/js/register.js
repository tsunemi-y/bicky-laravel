/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************!*\
  !*** ./resources/js/register.js ***!
  \**********************************/
// 表示・非表示制御対象要素リストを定義
// ※兄弟児がいる場合のみ入力する項目が対象
showedOrHidedTargetElementList = [$('#age2'), $('#gender2'), $('#diagnosis2')];
$(function () {
  onHandleChangeChildName2($('#childName2').val(), showedOrHidedTargetElementList);
});
$('#childName2').change(function () {
  onHandleChangeChildName2($(this).val(), showedOrHidedTargetElementList);
}); // 発火要素値の有無により、対象要素リストの表示・非表示を制御

function onHandleChangeChildName2(targetEventElementVal, targetElementList) {
  if (targetEventElementVal) {
    for (var i = 0; i < targetElementList.length; i++) {
      targetElementList[i].removeClass('d-none');
    }
  } else {
    for (var _i = 0; _i < targetElementList.length; _i++) {
      targetElementList[_i].addClass('d-none');
    } // 兄弟児氏名の削除時、他兄弟児要素の値を空に設定


    targetInputElementList = [$('input[name="age2"]'), $('input[name="diagnosis2"]')];

    for (var _i2 = 0; _i2 < targetInputElementList.length; _i2++) {
      targetInputElementList[_i2].val('');
    }
  }
} // LINE相談のみにチェック時、他の選択肢を非表示


$('#line').click(function () {
  var $numberOfUse = $('#numberOfUse');
  var $coursePlan = $('#coursePlan');

  if ($(this).prop("checked") == true) {
    $numberOfUse.hide();
    $coursePlan.hide();
  } else {
    $numberOfUse.show();
    $coursePlan.show();
  }
});
/******/ })()
;