import { z } from 'zod'

const MAX_FILE_SIZE = 2097152;
const ACCEPTED_IMAGE_TYPES: string[] = ["image/jpeg", "image/jpg", "image/png"];


export const updateSchema = z.object({
    description: z.coerce.string().min(1),
    complaintStatus: z.coerce.string().min(1),
    attachment: z.any().optional()

}).superRefine(({ description, complaintStatus, attachment }, ctx) => {
    if (attachment) {
        if (attachment?.size > MAX_FILE_SIZE)
            ctx.addIssue({
                code: 'custom',
                message: 'File harus kurang dari 2MB.',
                path: ['attachment']
            })

        const type = ACCEPTED_IMAGE_TYPES.filter(val => val == attachment.type)

        if (type.length < 1)
            // attachment?.type
            ctx.addIssue({
                code: 'custom',
                message: "Hanya format .jpg, .jpeg, dan .png yang dapat digunakan.",
                path: ['attachment']
            })
    }
})

export type UpdateSchema = z.infer<typeof updateSchema>;



