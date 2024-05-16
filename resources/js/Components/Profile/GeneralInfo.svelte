<script lang="ts">
    import { page } from "@inertiajs/svelte";

    import { Button, Card, Input, Label, Select, Toast } from "flowbite-svelte";

    export let data: any;

    const user = $page.props.auth.user;
    let residentStatus = [
        { value: "PermanentResident", name: "Penduduk asli" },
        { value: "ContractResident", name: "Kontrak" },
        { value: "Kos", name: "Kos" },
    ];
</script>

<Card class="max-w-full">
    <h5
        class="mb-4 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
    >
        Informasi Umum
    </h5>
    <div class="mb-4">
        <Label for="fullName" class="mb-2">Nama Lengkap</Label>
        <Input
            id="fullName"
            placeholder="Nama Lengkap"
            name="fullName"
            value={data.data[0].civilian_id.fullName}
            disabled
        />
    </div>
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="mb-4">
            <Label for="residentstatus" class="mb-2">Status Kependudukan</Label>
            <Select
                name="residentStatus"
                placeholder="Status kependudukan"
                disabled
            >
                {#each residentStatus as { value, name }, idx}
                    {#if value == data.data[0].civilian_id.residentstatus}
                        <option {value} selected>{name}</option>
                    {:else}
                        <option {value}>{name}</option>
                    {/if}
                {/each}
            </Select>
        </div>
        <div class="mb-4">
            <Label for="job" class="mb-2">Pekerjaan</Label>
            <Input
                id="job"
                placeholder="Pekerjaan"
                name="job"
                value={data.data[0].civilian_id.job}
                disabled
            />
        </div>
    </div>
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="mb-4">
            <Label for="nik" class="mb-2">NIK</Label>
            <Input
                id="nik"
                placeholder="NIK"
                name="nik"
                value={data.data[0].civilian_id.nik}
                disabled
            />
        </div>
        <div class="mb-4">
            <Label for="nkk" class="mb-2">NKK</Label>
            <Input
                id="nkk"
                placeholder="123"
                name="nkk"
                value={data.data[0].civilian_id.nkk}
                disabled
            />
        </div>
    </div>
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="mb-4">
            <Label for="address" class="mb-2">Alamat</Label>
            <Input
                id="address"
                placeholder="Alamat"
                name="address"
                value={data.data[0].civilian_id.address}
                disabled
            />
        </div>
        <div class="mb-4">
            <Label for="no_hp" class="mb-2">No Hp</Label>
            <Input
                id="no_hp"
                placeholder="No Hp"
                name="phone"
                value={data.data[0].civilian_id.phone}
                disabled
            />
        </div>
    </div>

    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="">
            <Label for="birthdate" class="mb-2 w-full text-center"
                >Tempat, Tanggal Lahir</Label
            >

            <div class="mb-4 grid max-xl:grid-cols-1 grid-cols-2 gap-2">
                <Input
                    id="birthplace"
                    name="birthplace"
                    placeholder="Tempat Lahir"
                    value={data.data[0].civilian_id.birthplace}
                    disabled
                />

                <Input
                    id="birthdate"
                    placeholder="Tanggal Lahir"
                    type="date"
                    name="birthdate"
                    value={new Date(data.data[0].civilian_id.birthdate * 1000)
                        .toISOString()
                        .substring(0, 10)}
                    disabled
                />
            </div>
        </div>
        <div class="mb-4">
            <Label for="role" class="mb-2">Peran</Label>
            <Input
                id="role"
                disabled
                placeholder="Peran"
                name="role"
                value={user.role}
            />
        </div>
    </div>
    <!-- <div class="block flex"> -->
    <!--     <Button type="submit" class="ml-auto">Simpan Perubahan</Button> -->
    <!-- </div> -->
</Card>
