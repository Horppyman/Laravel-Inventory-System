<?php

namespace App\Http\Controllers\v1;

use Throwable;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/employees",
     * summary="Get All Employees",
     * description="Get All Employees",
     * operationId="getEmployees",
     * tags={"Employees"},
     
     * @OA\Response(
     *    response=422,
     *    description="Error",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Server Error")
     *        )
     *     )
     * )
     */

    public function index()
    {
        $employee = Employee::all();
        return response()->json($employee);
    }

      /**
     * @OA\Post(
     * path="/api/v1/employee",
     * summary="Add Employee",
     * description="Add Employee",
     * operationId="createEmployee",
     * tags={"Employees"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass employee credentials",
     *    @OA\JsonContent(
     *       required={"name","email", "phone", "sallary", "address", "nid"},
     *       @OA\Property(property="name", type="string", format="text", example="Your Name"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="phone", type="string", format="text", example="08005050505"),
     *       @OA\Property(property="sallary", type="string", format="text", example="50000"),
     *       @OA\Property(property="address", type="string", format="text", example="I Live on mars"),
     *       @OA\Property(property="nid", type="string", format="text", example="EMID1234"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Check all credentials and try again.")
     *        )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'name' => 'required|unique:employees|max:255',
            'email' => 'required|unique:employees',
            'phone' => 'required|unique:employees',
            'sallary' => 'required',
            'address' => 'required',
            'nid' => 'required'
        ]);

        if ($request->photo){
            try {
                $random = Str::random(10);
                $image_path = "/images/employees/$random.jpg";

                Storage::disk('public')->put(
                    $image_path,       
                    base64_decode(explode(',', $request->photo)[0])       
                );

                $employee = new Employee;
                $employee->name = $request->name;
                $employee->email = $request->email;
                $employee->phone = $request->phone;
                $employee->sallary = $request->sallary;
                $employee->address = $request->address;
                $employee->nid = $request->nid;
                $employee->joining_date = $request->joining_date;
                $employee->photo = $image_path;
                $employee->save();
                return response()->json('employee added succesfully', 200);
            } catch(Throwable $err) {
                return $err->getMessage();
            }   
        } else {
            try{
                $employee = new Employee;
                $employee->create($request->all());
                return response()->json('employee added succesfully', 200);
            } catch(Throwable $err) {
                return $err->getMessage();
            }
            
        }
    }

    /**
     * @OA\Get(
     *      path="/employee/{id}",
     *      operationId="getEmployeeById",
     *      tags={"Employees"},
     *      summary="Get employee information",
     *      description="Returns employee data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Employee id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function show($id)
    {
        $employee = DB::table('employees')->where('id', $id)->first();
        return response()->json($employee);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/update-employee/{id}",
     *      operationId="updateSingleEmployee",
     *      tags={"Employees"},
     *      summary="Get employee information",
     *      description="Returns employee data",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass employee credentials",
     *    @OA\JsonContent(
     *       required={"name","email", "phone", "sallary", "address", "nid"},
     *       @OA\Property(property="name", type="string", format="text", example="Your Name"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="phone", type="string", format="text", example="08005050505"),
     *       @OA\Property(property="sallary", type="string", format="text", example="50000"),
     *       @OA\Property(property="address", type="string", format="text", example="I Live on mars"),
     *       @OA\Property(property="nid", type="string", format="text", example="EMID1234"),
     *    ),
     * ),
     *      @OA\Parameter(
     *          name="id",
     *          description="Employee id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Project")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $fields = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required',
            'phone' => 'required',
            'sallary' => 'required',
            'address' => 'required',
            'nid' => 'required'
        ]);

        if ($request->photo){
            try {
                $random = Str::random(10);
                $image_path = "/images/employees/$random.jpg";

                Storage::disk('public')->put(
                    $image_path,       
                    base64_decode(explode(',', $request->photo)[0])       
                );

                $employee = Employee::findOrFail($id);
                $employee->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'sallary' => $request->sallary,
                    'address' => $request->address,
                    'nid' => $request->nid,
                    'joining_date' => $request->joining_date,
                    'photo' => $request->photo,
                ]);
                return response()->json('employee record updated succesfully', 200);
            } catch(Throwable $err) {
                return $err->getMessage();
            }   
        } else {
            try{
                $employee = Employee::findOrFail($id);
                $employee->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'sallary' => $request->sallary,
                    'address' => $request->address,
                    'nid' => $request->nid,
                    'joining_date' => $request->joining_date,
                ]);
                return response()->json('employee record updated succesfully', 200);
            } catch(Throwable $err) {
                return $err->getMessage();
            }
            
        }
    }

    /**
         * @OA\Delete(
         *      path="/api/v1/delete-employee/{id}",
         *      operationId="deleteEmployee",
         *      tags={"Employees"},
         *      summary="Delete existing employee",
         *      description="Deletes a record and returns a status code ",
         *      @OA\Parameter(
         *          name="id",
         *          description="Employee id",
         *          required=true,
         *          in="path",
         *          @OA\Schema(
         *              type="integer"
         *          )
         *      ),
         *      @OA\Response(
         *          response=204,
         *          description="Successful operation",
         *          @OA\JsonContent()
         *       ),
         *      @OA\Response(
         *          response=401,
         *          description="Unauthenticated",
         *      ),
         *      @OA\Response(
         *          response=403,
         *          description="Forbidden"
         *      ),
         *      @OA\Response(
         *          response=404,
         *          description="Resource Not Found"
         *      )
         * )
         */

    public function destroy($id)
    {
        $employee = DB::table('employees')->where('id', $id)->first();

        if($employee){
            try {
                DB::table('employees')->where('id', $id)->delete();
                return response()->json('Employee deleted successfully.', 200);
            } catch (Throwable $err) {
                return $err->getMessage();
            }
           
        } else {
            return response()->json('Employee not found.', 404);
        }
    }
}
