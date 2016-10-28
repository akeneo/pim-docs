How to Mass Import Assets
=========================

A command has been created in the PIM to allow you to mass import assets: ``php app/console pim:product-asset:mass-upload --user=[username]``

For instance, with the user "admin", the command will be: ``php app/console pim:product-asset:mass-upload --user=admin``

To make this command work, you need to copy the files you want to import into the following folder:

``[tmp_storage_dir]/mass_upload_imported/[username]/``

With:
``[tmp_storage_dir]`` the path configured in ``pim_parameters.yml`` under the parameter ``tmp_storage``.
``[username]`` the name of the user that processes the import.

The command will upload the files into the application.

For example, if you upload a file named "demo_video.avi", Akeneo PIM will check if an asset with the code "demo_video" exists.
If so, the application will update the asset "demo_video" by importing the file "demo_video.avi" as the new reference file for this asset (and will also generate its variations).
If no asset exists with the code "demo_video", the PIM will create this new asset and will generate its variations.

If you want to enrich these newly imported assets with other information (such as a description, tags, etc.), you need to create an import profile for the job "Asset import in CSV".
For the sake of our example, let's say the code of this profile is "assets_mass_upload".

The job accepts a CSV file in the same format as the one below (delimiters and escape characters can be configured in the interface):

.. csv-table:: Assets import
   :header-rows: 1
   :file: asset.csv

In the profile configuration of the import job, you can specify the path to the assets CSV file on the server, so that this file is imported when the job is executed.

Then, the job can be can ran with:
``php app/console akeneo:batch:job assets_mass_upload --env=prod``
