// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

define([], function() {

    /**
     * General utilities library.
     */
    return {
        /**
         * On function evaluating true.
         */
        whenTrue: function(func, callBack, forceCallBack, maxIterations, i) {
            maxIterations = !maxIterations ? 10 : maxIterations;
            i = !i ? 0 : i + 1;
            if (i > maxIterations) {
                // Error, too long waiting for function to evaluate true.
                if (forceCallBack) {
                    callBack();
                }
                return;
            }
            if (func()) {
                callBack();
            } else {
                var self = this;
                window.setTimeout(function() {
                    self.whenTrue(func, callBack, forceCallBack, maxIterations, i);
                }, 200);
            }
        }
    }

});