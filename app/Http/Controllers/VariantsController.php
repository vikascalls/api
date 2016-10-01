<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Attributes;
use App\Attributables;
use App\ProductVariants;

class VariantsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $variantId=func_get_arg(1);
            if(func_num_args()==3){
                $attributeName = func_get_arg(2);
            }
            $data = $request->all();
            $name = isset($attributeName)?$attributeName:$data['attribute'][0]['name'];
            $value = $data['attribute'][0]['value'];
            $attribute = Attributes::firstOrNew(['name' => $name]);
            $saveData = Attributables::firstOrNew([
                            'attribute_id'=>$attribute->id,
                            'value'=>$value,
                            'created_by'=>1
                        ]);
            if($attribute->exists){
                $productAttributes = ProductVariants::find($variantId)->attributables->where('attribute_id',$attribute->id);
                if(count($productAttributes)>0){
                    foreach($productAttributes as $productAttribute){
                        $productAttribute->value = $value;
                        $productAttribute->updated_by = 1;
                        $productAttribute->save();  
                    }
                }else{
                    $productAttribute = ProductVariants::find($variantId);
                    $productAttribute->attributables()->save(Attributables::firstOrNew([
                            'attribute_id'=>$attribute->id,
                            'value'=>$value,
                            'created_by'=>1
                        ]));
                }
            }else{
                if(!isset($attributeName)){
                    $attribute->name = $name;
                    $attribute->created_by = 1;
                    $attribute->save();
                    $saveData = Attributables::firstOrNew([
                                'attribute_id'=>$attribute->id,
                                'value'=>$value,
                                'created_by'=>1
                            ]);
                    $productAttribute = ProductVariants::find($variantId);
                    $productAttribute->attributables()->save(Attributables::firstOrNew([
                                'attribute_id'=>$attribute->id,
                                'value'=>$value,
                                'created_by'=>1
                            ]));
                }else{
                    return json_encode(array("Error: "=>"No attribute with the specified name found!"));
                }
            }
            return json_encode(array("id"=>$variantId,"variant_id"=>$variantId,"attribute_id"=>$attribute->id,"value"=>$value,"attribute_name"=>$name));
        } catch (Exception $ex) {
            return json_encode(array("error" => "Error: ".$ex->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}