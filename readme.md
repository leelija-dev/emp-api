# Employee Management API Documentation
## This file structure explains the various API methods and their details as requested.

## 1. Get Employee Details by Employee ID

- **Method:** GET
- **URL:** `<BASE_URL>/getEmployeeDetails/<EMPLOYEE_ID>`
- **Function:** `getEmployeeDetails($id)`
- **Description:** Fetch the details of an employee using the `emp_id`.

## 2. Get All Employee Names

- **Method:** GET
- **URL:** `http://localhost/leelija/employees`
- **Function:** `getEmployeesName`
- **Description:** Retrieve the names of all employees.

## 3. Get All Employee Details

- **Method:** GET
- **URL:** `http://localhost/leelija/employee-details`
- **Function:** `getEmployeesDetails`
- **Description:** Fetch the complete details of all employees.

## 4. Update Employee Documents by Document ID

- **Method:** POST
- **URL:** `http://localhost/leelija/emp/update-doc/<DOC_ID>`
- **Function:** `updateEmployeeDoc()`
- **Description:** Update employee documents where `updated_by` and `emp_id` are mandatory fields.

## 5. Update Employee Details by Employee ID

- **Method:** POST
- **URL:** `http://localhost/leelija/emp/update/<EMPLOYEE_ID>`
- **Function:** `updateEmployeeDetails()`
- **Description:** Update the details of an employee using `emp_id`.