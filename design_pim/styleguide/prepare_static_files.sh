#!/bin/bash

set -e

TARGET_DIR=$(dirname $0)/../../pim-docs-build/public

SRC_SITE=http://demo.akeneo.com

FILES="
    bundles/pimui/images/bulk/icon-edit.svg
    bundles/pimui/images/bulk/icon-edit_selected.svg
    bundles/pimui/images/bulk/icon-enable.svg
    bundles/pimui/images/bulk/icon-enable_selected.svg
    bundles/pimui/images/bulk/icon-folder_in.svg
    bundles/pimui/images/bulk/icon-folder_in_selected.svg
    bundles/pimui/images/bulk/icon-folder_move.svg
    bundles/pimui/images/bulk/icon-folder_move_selected.svg
    bundles/pimui/images/bulk/icon-folder_out.svg
    bundles/pimui/images/bulk/icon-folder_out_selected.svg
    bundles/pimui/images/bulk/icon-groups.svg
    bundles/pimui/images/bulk/icon-groups_selected.svg
    bundles/pimui/images/bulk/icon-template.svg
    bundles/pimui/images/bulk/icon-template_selected.svg
    bundles/pimui/images/icon-delete-bluedark.svg
    bundles/pimui/images/icon-infos.svg
    bundles/pimui/images/info-user.png
    bundles/pimui/images/logo.svg
    css/pim.css
"

mkdir -p $TARGET_DIR/css $TARGET_DIR/bundles/pimui/images/bulk

for FILE in $FILES; do
    wget -nv $SRC_SITE/$FILE -O $TARGET_DIR/$FILE
done
