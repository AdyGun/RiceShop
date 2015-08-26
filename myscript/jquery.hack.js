/*! JQuery Function hack.js
 * ========================
 * Additional function for JQuery
 *
 * @Author  AdyGun
 * @Email   <adygun91@gmail.com>
 * @version 0.0.1
 */

$.fn.clearForm = function() {
  return this.each(function() {
    var type = this.type, tag = this.tagName.toLowerCase();
    if (tag == 'form')
      return $(':input',this).clearForm();
    if (type == 'text' || type == 'password' || tag == 'textarea')
      this.value = '';
    else if (type == 'checkbox' || type == 'radio')
      this.checked = false;
    else if (tag == 'select')
      this.selectedIndex = -1;
  });
};