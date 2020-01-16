#!/bin/bash
function init(){
	PEER=$1
	ORG=$2
	CORE_PEER_MSPCONFIGPATH=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org${ORG}.example.com/users/Admin@org${ORG}.example.com/msp
	CORE_PEER_ADDRESS=peer${PEER}.org${ORG}.example.com:$((2*$ORG+$PEER+5))051
	CORE_PEER_LOCALMSPID="Org${ORG}MSP"
	CORE_PEER_TLS_ROOTCERT_FILE=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org${ORG}.example.com/peers/peer${PEER}.org${ORG}.example.com/tls/ca.crt
}
ORDERER_CA=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/ordererOrganizations/example.com/orderers/orderer.example.com/msp/tlscacerts/tlsca.example.com-cert.pem

peer=$1
org=$2
Id=$3
name=$4
dob=$5
bank=$6
phone=$7
aadhar=$8
file=$9
hash=${10}
coll=${11}
init $peer $org

export PRIVATE=$(echo -n "{\"aadhar\":\"${aadhar}\",\"hash\":\"${hash}\",\"phone\":\"${phone}\",\"file\":\"${file}\",\"coll\":\"${coll}\"}" | base64 | tr -d \\n)
peer chaincode invoke -o orderer.example.com:7050 --tls true --cafile $ORDERER_CA -C mychannel -n mycc --peerAddresses $CORE_PEER_ADDRESS --tlsRootCertFiles $CORE_PEER_TLS_ROOTCERT_FILE  -c '{"Args":["Apply","'$Id'","'$name'","'$dob'","'$bank'"]}'  --transient "{\"private\":\"$PRIVATE\"}"
