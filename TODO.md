# TODO: Update Postman Collection

## Information Gathered
- Analyzed the Laravel routes and compared with the existing Postman collection.
- The collection is mostly complete but missing some APIs.
- Missing APIs:
  - Auth: forgot-password, reset-password, update profile
  - Files: List Files (GET /files), Download File (GET /files/{file}/download), Delete File (DELETE /files/{file})

## Plan
- Update the Postman collection JSON to include the missing APIs.
- Add the missing auth endpoints under the Authentication folder.
- Add the missing file endpoints under the Files folder.

## Dependent Files to be edited
- Chat_App_API.postman_collection.json

## Followup steps
- Verify the collection is updated correctly.
- Ensure all endpoints match the Laravel routes.
