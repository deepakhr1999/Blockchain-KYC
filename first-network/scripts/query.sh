#!/bin/bash
function init(){
	PEER=$1
	ORG=$2
	CORE_PEER_MSPCONFIGPATH=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org${ORG}.example.com/users/Admin@org${ORG}.example.com/msp
	CORE_PEER_ADDRESS=peer${PEER}.org${ORG}.example.com:7051
	CORE_PEER_LOCALMSPID="Org${ORG}MSP"
	CORE_PEER_TLS_ROOTCERT_FILE=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org${ORG}.example.com/peers/peer${PEER}.org${ORG}.example.com/tls/ca.crt
}
func=$1
peer=$2
org=$3
username=$4
init $peer $org
if [ $func = "public" ]; then
	peer chaincode query -C mychannel -n mycc -c '{"Args":["Query","'$username'"]}'
elif [ $func = "private" ]; then
	peer chaincode query -C mychannel -n mycc -c '{"Args":["QueryPrivate","'$username'"]}'
else
	echo Invalid;
fi