package main

import (
	"encoding/json"
	"fmt"

	"github.com/hyperledger/fabric/core/chaincode/shim"
	pb "github.com/hyperledger/fabric/protos/peer"
)


// SimpleChaincode example simple Chaincode implementation
type SimpleChaincode struct {
}

type Document struct{
	Name   	string `json:"name"`
	Dob 	string `json:"dob"`
	Flag	string `json:"flag"`
	Bank	string `json:"bank"`	
}

type PrivData struct{
	Aadhar 	string `json:"aadhar"`
	File 	string `json:"file"`
	Hash 	string `json:"hash"`
	Phone	string `json:"phone"`
}

type TransPriv struct{
	Aadhar 	string `json:"aadhar"`
	File 	string `json:"file"`
	Hash 	string `json:"hash"`
	Phone	string `json:"phone"`
	Coll	string `json:"coll"`
}

func (t *SimpleChaincode) Init(stub shim.ChaincodeStubInterface) pb.Response{
	return shim.Success(nil)
}


func (t *SimpleChaincode) Invoke(stub shim.ChaincodeStubInterface) pb.Response {
	// fmt.Println("ex02 Invoke")
	function, args := stub.GetFunctionAndParameters()
	if function == "Query" {
		return t.Query(stub, args)
	}else if function == "Apply"{
		return t.Apply(stub, args)
	}else if function == "Validate"{
		return t.Validate(stub, args)
	}else if function == "Delete"{
		return t.Delete(stub, args)
	}else if function == "QueryPrivate"{
		return t.QueryPrivate(stub, args)
	}
	return shim.Error("Invalid invoke function name. Expecting \"Query\" \"QueryPrivate\" \"Apply\" \"Validate\"")
}

/*Query the public state of the ledger*/
func (t *SimpleChaincode) Query(stub shim.ChaincodeStubInterface, args []string) pb.Response{
	if len(args)!= 1{
		return shim.Error("Incorrect arguments, expecting 1")
	}
	
	var name = args[0]

	// Get the state from the ledger
	state_b, err := stub.GetState(name)
	if err != nil {
		return shim.Error("Could not get state")
	}

	//return the state as bytes
	return shim.Success(state_b)
}

func (t *SimpleChaincode) Apply(stub shim.ChaincodeStubInterface, args []string) pb.Response{
	if len(args)!= 4{
		return shim.Error("Incorrect arguments, expecting 4")
	}

	//assemble data
	var username = args[0]
	var state = &Document{
		Name: args[1],
		Dob: args[2],
		Bank: args[3],
		Flag: "No",
	}

	//get transient data
	transMap, err := stub.GetTransient()
	if err != nil {
		return shim.Error("Error getting transient: " + err.Error())
	}

	//check if state already exists
	state_b, err := stub.GetState(username)
	if state_b != nil {
		return shim.Error("Given username already exists!")
	}

	//marshal
	state_b, err = json.Marshal(state)
	if err != nil {
		return shim.Error(err.Error())
	}

	var transient TransPriv
	json.Unmarshal(transMap["private"], &transient)
	var priv = &PrivData{
		Aadhar: transient.Aadhar,
		File: transient.File,
		Hash: transient.Hash,
		Phone: transient.Phone,
	}

	private_b, err := json.Marshal(priv)
	if err != nil {
		return shim.Error(err.Error())
	}

	//putstate
	err = stub.PutState(username, state_b)
	if err != nil {
		return shim.Error(err.Error())
	}
	err = stub.PutPrivateData(transient.Coll, username, private_b)
	if err != nil {
		return shim.Error(err.Error())
	}


	return shim.Success(nil)
}

func (t *SimpleChaincode) Validate(stub shim.ChaincodeStubInterface, args []string) pb.Response{
	if len(args)!= 1{
		return shim.Error("Incorrect arguments, expecting 1")
	}

	//get state as bytes
	var username = args[0]
	state_b, err := stub.GetState(username)
	if err!=nil{
		return shim.Error("Username does not exist")
	}

	//umarshal and edit
	var state Document
	json.Unmarshal(state_b, &state)
	state.Flag = "Yes"

	//marshal
	state_b, err = json.Marshal(state)
	if err != nil {
		return shim.Error(err.Error())
	}

	//putstate
	stub.PutState(username, state_b)

	return shim.Success(nil)
}

// Gets data from private data collection
func (t *SimpleChaincode) QueryPrivate(stub shim.ChaincodeStubInterface, args []string) pb.Response{
	if len(args)!=2{
		return shim.Error("Incorrect arguments, expecting 2")
	}

	username := args[0]
	coll := args[1]
	//check if the state under username has been deleted
	state_b, err := stub.GetState(username)
	if state_b == nil {
		return shim.Error("User does not exist")
	}

	private_b, err := stub.GetPrivateData(coll, username) 
     if err != nil {
             return shim.Error("Failed to get private details for "+username)
     } else if private_b == nil {
             return shim.Error("Private details do not exist for "+username)
     }
	return shim.Success(private_b)
}

// Deletes an entity from state
func (t *SimpleChaincode) Delete(stub shim.ChaincodeStubInterface, args []string) pb.Response {
	if len(args) != 1 {
		return shim.Error("Incorrect number of arguments. Expecting 1")
	}

	username := args[0]

	// Delete the key from the state in ledger
	err := stub.DelState(username)
	if err != nil {
		return shim.Error("Failed to delete state")
	}

	return shim.Success(nil)
}


func main() {
	err := shim.Start(new(SimpleChaincode))
	if err != nil {
		fmt.Printf("Error starting Simple chaincode: %s", err)
	}
}
