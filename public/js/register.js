/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************!*\
  !*** ./resources/js/register.js ***!
  \**********************************/
// 表示・非表示制御対象要素リストを定義
// ※兄弟児がいる場合のみ入力する項目が対象
showedOrHidedTargetElementList = [$('#age2'), $('#gender2'), $('#diagnosis2')];
$(function () {
  showOrHideTargetElementList($('#childName2').val(), showedOrHidedTargetElementList);
});
$('#childName2').change(function () {
  showOrHideTargetElementList($(this).val(), showedOrHidedTargetElementList);
}); // 発火要素値の有無により、対象要素リストの表示・非表示を制御

function showOrHideTargetElementList(targetEventElementVal, targetElementList) {
  if (targetEventElementVal) {
    for (var i = 0; i < targetElementList.length; i++) {
      targetElementList[i].removeClass('d-none');
    }
  } else {
    for (var _i = 0; _i < targetElementList.length; _i++) {
      targetElementList[_i].addClass('d-none');
    }
  }
}
/******/ })()
;